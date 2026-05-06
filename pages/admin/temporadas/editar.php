<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$es_edicion = ($id > 0);
$temporada = ['anio' => date('Y'), 'fecha_inicio' => '', 'fecha_fin' => '', 'prize_pool' => ''];
$error = '';

if ($es_edicion) {
    $res = mysqli_query($conexion, "SELECT * FROM TEMPORADA WHERE id_temporada = $id");
    $temporada = mysqli_fetch_assoc($res);
    if (!$temporada) {
        $_SESSION['mensaje_error'] = "Temporada no encontrada.";
        header("Location: /RLCS/CRM/pages/admin/temporadas/index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anio        = intval($_POST['anio'] ?? 0);
    $fecha_inicio = mysqli_real_escape_string($conexion, $_POST['fecha_inicio'] ?? '');
    $fecha_fin    = mysqli_real_escape_string($conexion, $_POST['fecha_fin'] ?? '');
    $prize_pool   = !empty($_POST['prize_pool']) ? floatval($_POST['prize_pool']) : 'NULL';

    if ($anio < 2015 || $anio > 2100 || empty($fecha_inicio) || empty($fecha_fin)) {
        $error = "Año, fecha de inicio y fecha de fin son obligatorios.";
    } elseif ($fecha_fin < $fecha_inicio) {
        $error = "La fecha de fin no puede ser anterior a la de inicio.";
    } else {
        $prize_sql = is_float($prize_pool) ? $prize_pool : 'NULL';

        if ($es_edicion) {
            $sql = "UPDATE TEMPORADA SET
                    anio = $anio, fecha_inicio = '$fecha_inicio',
                    fecha_fin = '$fecha_fin', prize_pool = $prize_sql
                    WHERE id_temporada = $id";
        } else {
            $sql = "INSERT INTO TEMPORADA (anio, fecha_inicio, fecha_fin, prize_pool)
                    VALUES ($anio, '$fecha_inicio', '$fecha_fin', $prize_sql)";
        }

        if (mysqli_query($conexion, $sql)) {
            $_SESSION['mensaje_exito'] = $es_edicion ? "Temporada actualizada." : "Temporada creada.";
            header("Location: /RLCS/CRM/pages/admin/temporadas/index.php");
            exit();
        } else {
            $error = "Error al guardar: " . mysqli_error($conexion);
            error_log("Error temporada: " . mysqli_error($conexion));
        }
    }

    $temporada['anio']        = $_POST['anio'] ?? '';
    $temporada['fecha_inicio'] = $_POST['fecha_inicio'] ?? '';
    $temporada['fecha_fin']    = $_POST['fecha_fin'] ?? '';
    $temporada['prize_pool']   = $_POST['prize_pool'] ?? '';
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-calendar3"></i>
        <?= $es_edicion ? 'Editar Temporada' : 'Nueva Temporada' ?>
    </h2>
    <a href="/RLCS/CRM/pages/admin/temporadas/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card bg-dark border-secondary" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-white">Año *</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           name="anio" required min="2015" max="2100"
                           value="<?= htmlspecialchars($temporada['anio']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Fecha Inicio *</label>
                    <input type="date" class="form-control bg-dark text-white border-secondary"
                           name="fecha_inicio" required
                           value="<?= htmlspecialchars($temporada['fecha_inicio']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-white">Fecha Fin *</label>
                    <input type="date" class="form-control bg-dark text-white border-secondary"
                           name="fecha_fin" required
                           value="<?= htmlspecialchars($temporada['fecha_fin']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white">Prize Pool ($)</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           name="prize_pool" min="0" step="0.01"
                           value="<?= htmlspecialchars($temporada['prize_pool'] ?? '') ?>"
                           placeholder="Ej: 6000000">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg"></i> <?= $es_edicion ? 'Guardar Cambios' : 'Crear Temporada' ?>
                </button>
                <a href="/RLCS/CRM/pages/admin/temporadas/index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
