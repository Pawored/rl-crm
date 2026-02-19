<?php
/**
 * JUGADORES - Editar / Crear jugador
 * Con ?id=X → edita jugador (UPDATE).
 * Sin ?id → crea jugador nuevo (INSERT).
 * Incluye sección para transferir jugador a otro equipo.
 * Solo accesible por admin y editor.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// --- Solo admin y editor pueden acceder ---
requiereRol(['admin', 'editor']);

// === LÓGICA PHP ===

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$es_edicion = ($id > 0);
$jugador = ['nickname' => '', 'nombre_real' => '', 'fecha_nacimiento' => '', 'pais' => ''];
$error = '';
$error_transfer = '';

// --- Si es edición, cargar datos del jugador ---
if ($es_edicion) {
    $sql = "SELECT * FROM JUGADOR WHERE id_jugador = $id";
    $res = mysqli_query($conexion, $sql);
    $jugador = mysqli_fetch_assoc($res);

    if (!$jugador) {
        $_SESSION['mensaje_error'] = "Jugador no encontrado.";
        header("Location: /RLCS/CRM/pages/jugadores/index.php");
        exit();
    }
}

// --- Obtener equipos activos para el dropdown de transferencia ---
$sql_equipos = "SELECT id_equipo, nombre, tag FROM EQUIPO WHERE activo = 1 ORDER BY nombre";
$res_equipos = mysqli_query($conexion, $sql_equipos);

// --- Procesar transferencia ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'transferir') {
    $id_equipo_nuevo = intval($_POST['id_equipo_nuevo'] ?? 0);
    $fecha_transfer  = mysqli_real_escape_string($conexion, $_POST['fecha_transferencia'] ?? date('Y-m-d'));

    if ($id_equipo_nuevo <= 0) {
        $error_transfer = "Debes seleccionar un equipo.";
    } else {
        // Llamar al procedimiento almacenado de transferencia
        $sql_transfer = "CALL transferir_jugador($id, $id_equipo_nuevo, '$fecha_transfer')";
        if (mysqli_query($conexion, $sql_transfer)) {
            $_SESSION['mensaje_exito'] = "Jugador transferido correctamente.";
            header("Location: /RLCS/CRM/pages/jugadores/detalle.php?id=$id");
            exit();
        } else {
            $error_transfer = "Error al transferir: " . mysqli_error($conexion);
        }
    }
}

// --- Procesar formulario de datos del jugador ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['accion']) || $_POST['accion'] === 'guardar')) {
    $nickname         = mysqli_real_escape_string($conexion, trim($_POST['nickname'] ?? ''));
    $nombre_real      = mysqli_real_escape_string($conexion, trim($_POST['nombre_real'] ?? ''));
    $fecha_nacimiento = mysqli_real_escape_string($conexion, trim($_POST['fecha_nacimiento'] ?? ''));
    $pais             = mysqli_real_escape_string($conexion, trim($_POST['pais'] ?? ''));

    if (empty($nickname)) {
        $error = "El nickname es obligatorio.";
    } else {
        if ($es_edicion) {
            // --- UPDATE ---
            $sql_update = "UPDATE JUGADOR SET
                           nickname = '$nickname',
                           nombre_real = '$nombre_real',
                           fecha_nacimiento = " . (!empty($fecha_nacimiento) ? "'$fecha_nacimiento'" : "NULL") . ",
                           pais = '$pais'
                           WHERE id_jugador = $id";

            if (mysqli_query($conexion, $sql_update)) {
                $_SESSION['mensaje_exito'] = "Jugador actualizado correctamente.";
                header("Location: /RLCS/CRM/pages/jugadores/detalle.php?id=$id");
                exit();
            } else {
                $error = "Error al actualizar el jugador.";
                error_log("Error UPDATE jugador: " . mysqli_error($conexion));
            }
        } else {
            // --- INSERT ---
            $sql_insert = "INSERT INTO JUGADOR (nickname, nombre_real, fecha_nacimiento, pais)
                           VALUES ('$nickname', '$nombre_real',
                           " . (!empty($fecha_nacimiento) ? "'$fecha_nacimiento'" : "NULL") . ",
                           '$pais')";

            if (mysqli_query($conexion, $sql_insert)) {
                $nuevo_id = mysqli_insert_id($conexion);
                $_SESSION['mensaje_exito'] = "Jugador creado correctamente.";
                header("Location: /RLCS/CRM/pages/jugadores/detalle.php?id=$nuevo_id");
                exit();
            } else {
                $error = "Error al crear el jugador.";
                error_log("Error INSERT jugador: " . mysqli_error($conexion));
            }
        }
    }

    // Mantener datos si hay error
    $jugador['nickname']         = $_POST['nickname'] ?? '';
    $jugador['nombre_real']      = $_POST['nombre_real'] ?? '';
    $jugador['fecha_nacimiento'] = $_POST['fecha_nacimiento'] ?? '';
    $jugador['pais']             = $_POST['pais'] ?? '';
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-pencil-square"></i>
        <?= $es_edicion ? 'Editar Jugador' : 'Nuevo Jugador' ?>
    </h2>
    <a href="/RLCS/CRM/pages/jugadores/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- ========== FORMULARIO DE DATOS ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-person"></i> Datos del Jugador
        </h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="accion" value="guardar">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="nickname" class="form-label text-white">Nickname *</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           id="nickname" name="nickname" required
                           value="<?= htmlspecialchars($jugador['nickname']) ?>"
                           placeholder="Ej: Zen">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="nombre_real" class="form-label text-white">Nombre Real</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           id="nombre_real" name="nombre_real"
                           value="<?= htmlspecialchars($jugador['nombre_real'] ?? '') ?>"
                           placeholder="Ej: Finlay Ferguson">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_nacimiento" class="form-label text-white">Fecha de Nacimiento</label>
                    <input type="date" class="form-control bg-dark text-white border-secondary"
                           id="fecha_nacimiento" name="fecha_nacimiento"
                           value="<?= htmlspecialchars($jugador['fecha_nacimiento'] ?? '') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="pais" class="form-label text-white">País</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           id="pais" name="pais"
                           value="<?= htmlspecialchars($jugador['pais'] ?? '') ?>"
                           placeholder="Ej: Escocia">
                </div>
            </div>

            <button type="submit" class="btn btn-accent">
                <i class="bi bi-check-lg"></i>
                <?= $es_edicion ? 'Guardar Cambios' : 'Crear Jugador' ?>
            </button>
            <a href="/RLCS/CRM/pages/jugadores/index.php" class="btn btn-secondary ms-2">
                Cancelar
            </a>
        </form>
    </div>
</div>

<!-- ========== SECCIÓN TRANSFERIR (solo en edición) ========== -->
<?php if ($es_edicion): ?>
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-warning">
            <i class="bi bi-arrow-left-right"></i> Transferir Jugador
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($error_transfer)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error_transfer) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="accion" value="transferir">
            <div class="row">
                <!-- Dropdown de equipos -->
                <div class="col-md-5 mb-3">
                    <label for="id_equipo_nuevo" class="form-label text-white">Nuevo Equipo *</label>
                    <select class="form-select bg-dark text-white border-secondary"
                            id="id_equipo_nuevo" name="id_equipo_nuevo" required>
                        <option value="">-- Seleccionar equipo --</option>
                        <?php
                        mysqli_data_seek($res_equipos, 0);
                        while ($eq = mysqli_fetch_assoc($res_equipos)):
                        ?>
                            <option value="<?= $eq['id_equipo'] ?>">
                                <?= htmlspecialchars($eq['nombre']) ?>
                                [<?= htmlspecialchars($eq['tag']) ?>]
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- Fecha de transferencia -->
                <div class="col-md-4 mb-3">
                    <label for="fecha_transferencia" class="form-label text-white">Fecha de Transferencia</label>
                    <input type="date" class="form-control bg-dark text-white border-secondary"
                           id="fecha_transferencia" name="fecha_transferencia"
                           value="<?= date('Y-m-d') ?>">
                </div>
                <!-- Botón transferir -->
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="button" class="btn btn-warning w-100"
                            data-bs-toggle="modal" data-bs-target="#modalTransferir">
                        <i class="bi bi-arrow-left-right"></i> Transferir
                    </button>
                </div>
            </div>
        </form>

        <!-- Modal de confirmación de transferencia -->
        <div class="modal fade" id="modalTransferir" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Confirmar Transferencia
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que quieres transferir a
                        <strong><?= htmlspecialchars($jugador['nickname']) ?></strong>?
                        <br><small class="text-muted">Se cerrará su roster actual y se creará uno nuevo.</small>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-warning" id="btnConfirmarTransfer">
                            <i class="bi bi-arrow-left-right"></i> Confirmar Transferencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para enviar formulario de transferencia desde el modal -->
<script>
document.getElementById('btnConfirmarTransfer').addEventListener('click', function() {
    // Buscar el formulario de transferencia y enviarlo
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        const accion = form.querySelector('input[name="accion"]');
        if (accion && accion.value === 'transferir') {
            form.submit();
        }
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
