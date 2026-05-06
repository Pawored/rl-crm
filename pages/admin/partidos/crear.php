<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$error = '';

$torneos = mysqli_query($conexion,
    "SELECT t.id_torneo, t.nombre, temp.anio
     FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
     ORDER BY temp.anio DESC, t.nombre"
);
$equipos = mysqli_query($conexion,
    "SELECT e.id_equipo, e.nombre, e.tag, r.siglas AS region
     FROM EQUIPO e JOIN REGION r ON r.id_region = e.id_region
     WHERE e.activo = 1
     ORDER BY r.siglas, e.nombre"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_torneo  = intval($_POST['id_torneo'] ?? 0);
    $id_equipo1 = intval($_POST['id_equipo1'] ?? 0);
    $id_equipo2 = intval($_POST['id_equipo2'] ?? 0);
    $fecha_hora = mysqli_real_escape_string($conexion, $_POST['fecha_hora'] ?? '');
    $formato    = mysqli_real_escape_string($conexion, $_POST['formato'] ?? 'Bo5');

    if ($id_torneo <= 0 || $id_equipo1 <= 0 || $id_equipo2 <= 0 || empty($fecha_hora)) {
        $error = "Torneo, ambos equipos y fecha son obligatorios.";
    } elseif ($id_equipo1 === $id_equipo2) {
        $error = "Los dos equipos deben ser diferentes.";
    } else {
        $sql = "INSERT INTO PARTIDO (id_torneo, id_equipo1, id_equipo2, fecha_hora, formato)
                VALUES ($id_torneo, $id_equipo1, $id_equipo2, '$fecha_hora', '$formato')";

        if (mysqli_query($conexion, $sql)) {
            $_SESSION['mensaje_exito'] = "Partido creado correctamente.";
            header("Location: /RLCS/CRM/pages/admin/partidos/index.php");
            exit();
        } else {
            $error = "Error al crear el partido: " . mysqli_error($conexion);
        }
    }
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-joystick"></i> Nuevo Partido</h2>
    <a href="/RLCS/CRM/pages/admin/partidos/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card bg-dark border-secondary" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-white">Torneo *</label>
                <select class="form-select bg-dark text-white border-secondary" name="id_torneo" required>
                    <option value="">-- Seleccionar torneo --</option>
                    <?php while ($t = mysqli_fetch_assoc($torneos)): ?>
                    <option value="<?= $t['id_torneo'] ?>"
                            <?= (($_POST['id_torneo'] ?? 0) == $t['id_torneo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?> (<?= $t['anio'] ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-white">Equipo 1 *</label>
                    <select class="form-select bg-dark text-white border-secondary"
                            name="id_equipo1" id="equipo1" required>
                        <option value="">-- Seleccionar --</option>
                        <?php
                        mysqli_data_seek($equipos, 0);
                        $region_actual = '';
                        while ($e = mysqli_fetch_assoc($equipos)):
                            if ($e['region'] !== $region_actual) {
                                if ($region_actual !== '') echo '</optgroup>';
                                echo '<optgroup label="' . htmlspecialchars($e['region']) . '">';
                                $region_actual = $e['region'];
                            }
                        ?>
                        <option value="<?= $e['id_equipo'] ?>"
                                <?= (($_POST['id_equipo1'] ?? 0) == $e['id_equipo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?> [<?= htmlspecialchars($e['tag']) ?>]
                        </option>
                        <?php endwhile; if ($region_actual) echo '</optgroup>'; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white">Equipo 2 *</label>
                    <select class="form-select bg-dark text-white border-secondary"
                            name="id_equipo2" id="equipo2" required>
                        <option value="">-- Seleccionar --</option>
                        <?php
                        mysqli_data_seek($equipos, 0);
                        $region_actual = '';
                        while ($e = mysqli_fetch_assoc($equipos)):
                            if ($e['region'] !== $region_actual) {
                                if ($region_actual !== '') echo '</optgroup>';
                                echo '<optgroup label="' . htmlspecialchars($e['region']) . '">';
                                $region_actual = $e['region'];
                            }
                        ?>
                        <option value="<?= $e['id_equipo'] ?>"
                                <?= (($_POST['id_equipo2'] ?? 0) == $e['id_equipo']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?> [<?= htmlspecialchars($e['tag']) ?>]
                        </option>
                        <?php endwhile; if ($region_actual) echo '</optgroup>'; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white">Fecha y Hora *</label>
                    <input type="datetime-local" class="form-control bg-dark text-white border-secondary"
                           name="fecha_hora" required
                           value="<?= htmlspecialchars($_POST['fecha_hora'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-white">Formato</label>
                    <select class="form-select bg-dark text-white border-secondary" name="formato">
                        <?php foreach (['Bo5', 'Bo7', 'Bo3', 'Bo1'] as $fmt): ?>
                        <option value="<?= $fmt ?>"
                                <?= (($_POST['formato'] ?? 'Bo5') === $fmt) ? 'selected' : '' ?>>
                            <?= $fmt ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg"></i> Crear Partido
                </button>
                <a href="/RLCS/CRM/pages/admin/partidos/index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
