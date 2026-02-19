<?php
/**
 * EQUIPOS - Editar / Crear equipo
 * Con ?id=X → edita equipo existente (UPDATE).
 * Sin ?id → crea equipo nuevo (INSERT).
 * Solo accesible por admin y editor.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';

// --- Solo admin y editor pueden acceder ---
requiereRol(['admin', 'editor']);

// === LÓGICA PHP ===

// --- Determinar si es edición o creación ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$es_edicion = ($id > 0);
$equipo = ['nombre' => '', 'tag' => '', 'id_region' => '', 'activo' => 1];
$error = '';

// --- Si es edición, cargar datos actuales del equipo ---
if ($es_edicion) {
    $sql = "SELECT * FROM EQUIPO WHERE id_equipo = $id";
    $res = mysqli_query($conexion, $sql);
    $equipo = mysqli_fetch_assoc($res);

    if (!$equipo) {
        $_SESSION['mensaje_error'] = "Equipo no encontrado.";
        header("Location: /RLCS/CRM/pages/equipos/index.php");
        exit();
    }
}

// --- Obtener regiones para el dropdown ---
$sql_regiones = "SELECT id_region, nombre, siglas FROM REGION ORDER BY nombre";
$res_regiones = mysqli_query($conexion, $sql_regiones);

// --- Procesar formulario al enviar ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar datos
    $nombre    = mysqli_real_escape_string($conexion, trim($_POST['nombre'] ?? ''));
    $tag       = mysqli_real_escape_string($conexion, trim($_POST['tag'] ?? ''));
    $id_region = intval($_POST['id_region'] ?? 0);
    $activo    = isset($_POST['activo']) ? 1 : 0;

    // Validaciones
    if (empty($nombre) || empty($tag)) {
        $error = "El nombre y el tag son obligatorios.";
    } else {
        if ($es_edicion) {
            // --- UPDATE: actualizar equipo existente ---
            $sql_update = "UPDATE EQUIPO SET
                           nombre = '$nombre',
                           tag = '$tag',
                           id_region = " . ($id_region > 0 ? $id_region : 'NULL') . ",
                           activo = $activo,
                           fecha_modificacion = NOW()
                           WHERE id_equipo = $id";

            if (mysqli_query($conexion, $sql_update)) {
                $_SESSION['mensaje_exito'] = "Equipo actualizado correctamente.";
                header("Location: /RLCS/CRM/pages/equipos/detalle.php?id=$id");
                exit();
            } else {
                $error = "Error al actualizar el equipo.";
                error_log("Error UPDATE equipo: " . mysqli_error($conexion));
            }
        } else {
            // --- INSERT: crear nuevo equipo ---
            $sql_insert = "INSERT INTO EQUIPO (nombre, tag, id_region, activo, fecha_creacion)
                           VALUES ('$nombre', '$tag',
                           " . ($id_region > 0 ? $id_region : 'NULL') . ",
                           $activo, NOW())";

            if (mysqli_query($conexion, $sql_insert)) {
                $nuevo_id = mysqli_insert_id($conexion);
                $_SESSION['mensaje_exito'] = "Equipo creado correctamente.";
                header("Location: /RLCS/CRM/pages/equipos/detalle.php?id=$nuevo_id");
                exit();
            } else {
                $error = "Error al crear el equipo.";
                error_log("Error INSERT equipo: " . mysqli_error($conexion));
            }
        }
    }

    // Si hubo error, mantener los datos en el formulario
    $equipo['nombre']    = $_POST['nombre'] ?? '';
    $equipo['tag']       = $_POST['tag'] ?? '';
    $equipo['id_region'] = $_POST['id_region'] ?? '';
    $equipo['activo']    = isset($_POST['activo']) ? 1 : 0;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-pencil-square"></i>
        <?= $es_edicion ? 'Editar Equipo' : 'Nuevo Equipo' ?>
    </h2>
    <a href="/RLCS/CRM/pages/equipos/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<!-- Mensaje de error -->
<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- ========== FORMULARIO ========== -->
<div class="card bg-dark border-secondary">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <!-- Nombre -->
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label text-white">Nombre del Equipo *</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           id="nombre" name="nombre" required
                           value="<?= htmlspecialchars($equipo['nombre']) ?>"
                           placeholder="Ej: Team Vitality">
                </div>

                <!-- Tag -->
                <div class="col-md-3 mb-3">
                    <label for="tag" class="form-label text-white">Tag *</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           id="tag" name="tag" required maxlength="10"
                           value="<?= htmlspecialchars($equipo['tag']) ?>"
                           placeholder="Ej: VIT">
                </div>

                <!-- Región -->
                <div class="col-md-3 mb-3">
                    <label for="id_region" class="form-label text-white">Región</label>
                    <select class="form-select bg-dark text-white border-secondary"
                            id="id_region" name="id_region">
                        <option value="0">-- Sin región --</option>
                        <?php
                        mysqli_data_seek($res_regiones, 0);
                        while ($region = mysqli_fetch_assoc($res_regiones)):
                        ?>
                            <option value="<?= $region['id_region'] ?>"
                                    <?= ($equipo['id_region'] == $region['id_region']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region['nombre']) ?>
                                (<?= htmlspecialchars($region['siglas']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <!-- Activo -->
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo"
                           <?= $equipo['activo'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-white" for="activo">
                        Equipo activo
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg"></i>
                    <?= $es_edicion ? 'Guardar Cambios' : 'Crear Equipo' ?>
                </button>
                <a href="/RLCS/CRM/pages/equipos/index.php" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
