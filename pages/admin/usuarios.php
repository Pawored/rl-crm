<?php
/**
 * ADMIN - Gestión de usuarios
 * Solo accesible por administradores.
 * Tabla con usuarios, cambio de rol y activar/desactivar.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// --- Solo admin puede acceder ---
requiereRol('admin');

// === LÓGICA PHP ===

$error = '';

// --- Procesar cambio de rol ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    $id_usuario = intval($_POST['id_usuario'] ?? 0);

    // Evitar que el admin se modifique a sí mismo
    if ($id_usuario == $_SESSION['id_usuario']) {
        $error = "No puedes modificar tu propio usuario.";
    } elseif ($id_usuario > 0) {

        if ($_POST['accion'] === 'cambiar_rol') {
            // Cambiar rol del usuario
            $nuevo_rol = mysqli_real_escape_string($conexion, $_POST['nuevo_rol'] ?? 'viewer');

            // Validar que el rol sea válido
            if (in_array($nuevo_rol, ['admin', 'editor', 'viewer'])) {
                $sql_rol = "UPDATE USUARIOS SET rol = '$nuevo_rol' WHERE id_usuario = $id_usuario";
                if (mysqli_query($conexion, $sql_rol)) {
                    $_SESSION['mensaje_exito'] = "Rol actualizado correctamente.";
                } else {
                    $error = "Error al cambiar el rol.";
                }
            } else {
                $error = "Rol no válido.";
            }
        }

        if ($_POST['accion'] === 'toggle_activo') {
            // Activar o desactivar usuario
            $sql_toggle = "UPDATE USUARIOS SET activo = NOT activo WHERE id_usuario = $id_usuario";
            if (mysqli_query($conexion, $sql_toggle)) {
                $_SESSION['mensaje_exito'] = "Estado del usuario actualizado.";
            } else {
                $error = "Error al cambiar el estado.";
            }
        }

        if (empty($error)) {
            header("Location: /RLCS/CRM/pages/admin/usuarios.php");
            exit();
        }
    }
}

// --- Obtener todos los usuarios ---
$sql = "SELECT * FROM USUARIOS ORDER BY fecha_registro DESC";
$res_usuarios = mysqli_query($conexion, $sql);

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<h2 class="text-white mb-4">
    <i class="bi bi-shield-lock"></i> Administración de Usuarios
</h2>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- ========== TABLA DE USUARIOS ========== -->
<div class="table-responsive">
    <table class="table table-dark table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Fecha Registro</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_usuarios && mysqli_num_rows($res_usuarios) > 0): ?>
                <?php while ($u = mysqli_fetch_assoc($res_usuarios)): ?>
                    <?php
                    // Determinar clase del badge del rol
                    $badge_rol = match($u['rol']) {
                        'admin'  => 'bg-danger',
                        'editor' => 'bg-warning text-dark',
                        'viewer' => 'bg-info',
                        default  => 'bg-secondary'
                    };
                    $es_yo = ($u['id_usuario'] == $_SESSION['id_usuario']);
                    ?>
                    <tr class="<?= $es_yo ? 'table-row-highlight' : '' ?>">
                        <td class="text-muted">#<?= $u['id_usuario'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($u['nombre']) ?></strong>
                            <?= $es_yo ? '<span class="badge bg-secondary">Tú</span>' : '' ?>
                        </td>
                        <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if ($es_yo): ?>
                                <!-- No se puede cambiar el rol propio -->
                                <span class="badge <?= $badge_rol ?>"><?= ucfirst($u['rol']) ?></span>
                            <?php else: ?>
                                <!-- Dropdown para cambiar rol -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="cambiar_rol">
                                    <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                    <select name="nuevo_rol"
                                            class="form-select form-select-sm bg-dark text-white border-secondary d-inline-block"
                                            style="width: auto;"
                                            onchange="this.form.submit()">
                                        <option value="admin" <?= ($u['rol'] == 'admin') ? 'selected' : '' ?>>
                                            Admin
                                        </option>
                                        <option value="editor" <?= ($u['rol'] == 'editor') ? 'selected' : '' ?>>
                                            Editor
                                        </option>
                                        <option value="viewer" <?= ($u['rol'] == 'viewer') ? 'selected' : '' ?>>
                                            Viewer
                                        </option>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted">
                            <?= date('d/m/Y H:i', strtotime($u['fecha_registro'])) ?>
                        </td>
                        <td>
                            <?php if ($u['activo']): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if (!$es_yo): ?>
                                <!-- Botón activar/desactivar con modal -->
                                <button type="button" class="btn btn-sm
                                        <?= $u['activo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalToggle<?= $u['id_usuario'] ?>">
                                    <?php if ($u['activo']): ?>
                                        <i class="bi bi-person-x"></i> Desactivar
                                    <?php else: ?>
                                        <i class="bi bi-person-check"></i> Activar
                                    <?php endif; ?>
                                </button>

                                <!-- Modal de confirmación -->
                                <div class="modal fade" id="modalToggle<?= $u['id_usuario'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content bg-dark text-white">
                                            <div class="modal-header border-secondary">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle text-warning"></i>
                                                    Confirmar Acción
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                ¿Estás seguro de que quieres
                                                <strong><?= $u['activo'] ? 'desactivar' : 'activar' ?></strong>
                                                al usuario
                                                <strong><?= htmlspecialchars($u['nombre']) ?></strong>?
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="accion" value="toggle_activo">
                                                    <input type="hidden" name="id_usuario"
                                                           value="<?= $u['id_usuario'] ?>">
                                                    <button type="submit"
                                                            class="btn <?= $u['activo'] ? 'btn-danger' : 'btn-success' ?>">
                                                        <i class="bi bi-check-lg"></i> Confirmar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay usuarios registrados.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
