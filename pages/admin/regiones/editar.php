<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$es_edicion = ($id > 0);
$region = ['nombre' => '', 'siglas' => '', 'plazas_mundial' => 1];
$error = '';

if ($es_edicion) {
    $res = mysqli_query($conexion, "SELECT * FROM REGION WHERE id_region = $id");
    $region = mysqli_fetch_assoc($res);
    if (!$region) {
        $_SESSION['mensaje_error'] = "Región no encontrada.";
        header("Location: /RLCS/CRM/pages/admin/regiones/index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre         = mysqli_real_escape_string($conexion, trim($_POST['nombre'] ?? ''));
    $siglas         = mysqli_real_escape_string($conexion, trim(strtoupper($_POST['siglas'] ?? '')));
    $plazas_mundial = intval($_POST['plazas_mundial'] ?? 1);

    if (empty($nombre) || empty($siglas)) {
        $error = "Nombre y siglas son obligatorios.";
    } elseif ($plazas_mundial < 1) {
        $error = "Las plazas mundiales deben ser al menos 1.";
    } else {
        if ($es_edicion) {
            $sql = "UPDATE REGION SET nombre='$nombre', siglas='$siglas', plazas_mundial=$plazas_mundial
                    WHERE id_region=$id";
        } else {
            $sql = "INSERT INTO REGION (nombre, siglas, plazas_mundial)
                    VALUES ('$nombre', '$siglas', $plazas_mundial)";
        }

        if (mysqli_query($conexion, $sql)) {
            $_SESSION['mensaje_exito'] = $es_edicion ? "Región actualizada." : "Región creada.";
            header("Location: /RLCS/CRM/pages/admin/regiones/index.php");
            exit();
        } else {
            $error = "Error al guardar: " . mysqli_error($conexion);
        }
    }

    $region['nombre']         = $_POST['nombre'] ?? '';
    $region['siglas']         = $_POST['siglas'] ?? '';
    $region['plazas_mundial'] = $_POST['plazas_mundial'] ?? 1;
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-globe"></i>
        <?= $es_edicion ? 'Editar Región' : 'Nueva Región' ?>
    </h2>
    <a href="/RLCS/CRM/pages/admin/regiones/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card bg-dark border-secondary" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white">Nombre *</label>
                <input type="text" class="form-control bg-dark text-white border-secondary"
                       name="nombre" required
                       value="<?= htmlspecialchars($region['nombre']) ?>"
                       placeholder="Ej: North America">
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label text-white">Siglas *</label>
                    <input type="text" class="form-control bg-dark text-white border-secondary"
                           name="siglas" required maxlength="10"
                           value="<?= htmlspecialchars($region['siglas']) ?>"
                           placeholder="Ej: NA">
                </div>
                <div class="col-6">
                    <label class="form-label text-white">Plazas Mundial</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           name="plazas_mundial" required min="1" max="20"
                           value="<?= intval($region['plazas_mundial']) ?>">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg"></i> <?= $es_edicion ? 'Guardar Cambios' : 'Crear Región' ?>
                </button>
                <a href="/RLCS/CRM/pages/admin/regiones/index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
