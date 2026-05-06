<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$error = '';
$id_equipo = intval($_GET['equipo'] ?? $_POST['id_equipo'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion    = $_POST['accion'] ?? '';
    $id_equipo = intval($_POST['id_equipo'] ?? 0);

    if ($accion === 'agregar') {
        $id_jugador   = intval($_POST['id_jugador'] ?? 0);
        $fecha_inicio = mysqli_real_escape_string($conexion, $_POST['fecha_inicio'] ?? '');
        $titular      = isset($_POST['titular']) ? 1 : 0;

        if ($id_equipo <= 0 || $id_jugador <= 0 || empty($fecha_inicio)) {
            $error = "Equipo, jugador y fecha de inicio son obligatorios.";
        } else {
            $check = mysqli_query($conexion,
                "SELECT id_roster FROM ROSTER
                 WHERE id_equipo=$id_equipo AND id_jugador=$id_jugador AND fecha_fin IS NULL"
            );
            if (mysqli_num_rows($check) > 0) {
                $error = "Este jugador ya está activo en ese equipo.";
            } else {
                $sql = "INSERT INTO ROSTER (id_equipo, id_jugador, fecha_inicio, titular)
                        VALUES ($id_equipo, $id_jugador, '$fecha_inicio', $titular)";
                if (mysqli_query($conexion, $sql)) {
                    $_SESSION['mensaje_exito'] = "Jugador añadido al roster.";
                } else {
                    $error = "Error: " . mysqli_error($conexion);
                }
            }
        }
    }

    if ($accion === 'cerrar') {
        $id_roster  = intval($_POST['id_roster'] ?? 0);
        $fecha_fin  = mysqli_real_escape_string($conexion, $_POST['fecha_fin'] ?? date('Y-m-d'));
        mysqli_query($conexion,
            "UPDATE ROSTER SET fecha_fin='$fecha_fin' WHERE id_roster=$id_roster"
        );
        $_SESSION['mensaje_exito'] = "Entrada de roster cerrada.";
    }

    if ($accion === 'eliminar') {
        $id_roster = intval($_POST['id_roster'] ?? 0);
        mysqli_query($conexion, "DELETE FROM ROSTER WHERE id_roster=$id_roster");
        $_SESSION['mensaje_exito'] = "Entrada de roster eliminada.";
    }

    header("Location: /RLCS/CRM/pages/admin/roster/gestionar.php?equipo=$id_equipo");
    exit();
}

$equipos = mysqli_query($conexion,
    "SELECT e.id_equipo, e.nombre, e.tag, r.siglas
     FROM EQUIPO e JOIN REGION r ON r.id_region = e.id_region
     WHERE e.activo = 1
     ORDER BY r.siglas, e.nombre"
);

$roster_activo   = [];
$roster_historial = [];
$equipo_info = null;

if ($id_equipo > 0) {
    $res_info = mysqli_query($conexion,
        "SELECT e.nombre, e.tag, r.siglas FROM EQUIPO e
         JOIN REGION r ON r.id_region = e.id_region
         WHERE e.id_equipo = $id_equipo"
    );
    $equipo_info = mysqli_fetch_assoc($res_info);

    $res_activo = mysqli_query($conexion,
        "SELECT r.*, j.nickname, j.pais
         FROM ROSTER r JOIN JUGADOR j ON j.id_jugador = r.id_jugador
         WHERE r.id_equipo = $id_equipo AND r.fecha_fin IS NULL
         ORDER BY r.titular DESC, j.nickname"
    );
    while ($row = mysqli_fetch_assoc($res_activo)) $roster_activo[] = $row;

    $res_hist = mysqli_query($conexion,
        "SELECT r.*, j.nickname
         FROM ROSTER r JOIN JUGADOR j ON j.id_jugador = r.id_jugador
         WHERE r.id_equipo = $id_equipo AND r.fecha_fin IS NOT NULL
         ORDER BY r.fecha_fin DESC LIMIT 20"
    );
    while ($row = mysqli_fetch_assoc($res_hist)) $roster_historial[] = $row;
}

// Jugadores disponibles (sin roster activo en este equipo)
$ids_activos = array_column($roster_activo, 'id_jugador');
$not_in = $ids_activos ? 'AND j.id_jugador NOT IN (' . implode(',', $ids_activos) . ')' : '';
$jugadores_libres = mysqli_query($conexion,
    "SELECT j.id_jugador, j.nickname, j.pais FROM JUGADOR j $not_in ORDER BY j.nickname"
    // Note: $not_in uses a WHERE condition but needs WHERE keyword
);
// Fix: properly handle WHERE
$jugadores_libres = mysqli_query($conexion,
    "SELECT j.id_jugador, j.nickname, j.pais FROM JUGADOR j
     WHERE 1=1 $not_in ORDER BY j.nickname"
);

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-people"></i> Gestión de Roster</h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Selector de equipo -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1" style="max-width:350px;">
                <label class="form-label text-white small">Seleccionar Equipo</label>
                <select name="equipo" class="form-select bg-dark text-white border-secondary">
                    <option value="0">-- Seleccionar equipo --</option>
                    <?php
                    $region_actual = '';
                    while ($e = mysqli_fetch_assoc($equipos)):
                        if ($e['siglas'] !== $region_actual) {
                            if ($region_actual !== '') echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($e['siglas']) . '">';
                            $region_actual = $e['siglas'];
                        }
                    ?>
                    <option value="<?= $e['id_equipo'] ?>" <?= $id_equipo == $e['id_equipo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre']) ?> [<?= htmlspecialchars($e['tag']) ?>]
                    </option>
                    <?php endwhile; if ($region_actual) echo '</optgroup>'; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-info">
                <i class="bi bi-funnel"></i> Ver
            </button>
        </form>
    </div>
</div>

<?php if ($id_equipo > 0 && $equipo_info): ?>

<!-- Roster activo -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header border-secondary d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-accent">
            <i class="bi bi-person-check"></i>
            Roster Activo — <?= htmlspecialchars($equipo_info['nombre']) ?>
            <span class="badge bg-secondary ms-2"><?= count($roster_activo) ?></span>
        </h6>
    </div>
    <div class="card-body">
        <?php if (!empty($roster_activo)): ?>
        <div class="table-responsive mb-3">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>País</th>
                        <th>Rol</th>
                        <th>Desde</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($roster_activo as $r): ?>
                <tr>
                    <td>
                        <a href="/RLCS/CRM/pages/jugadores/detalle.php?id=<?= $r['id_jugador'] ?>"
                           class="text-white text-decoration-none fw-semibold">
                            <?= htmlspecialchars($r['nickname']) ?>
                        </a>
                    </td>
                    <td class="text-muted small"><?= htmlspecialchars($r['pais'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= $r['titular'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $r['titular'] ? 'Titular' : 'Suplente' ?>
                        </span>
                    </td>
                    <td class="text-muted small"><?= date('d/m/Y', strtotime($r['fecha_inicio'])) ?></td>
                    <td class="text-center">
                        <!-- Cerrar entrada -->
                        <button class="btn btn-sm btn-outline-warning"
                                data-bs-toggle="collapse"
                                data-bs-target="#cerrar<?= $r['id_roster'] ?>">
                            <i class="bi bi-calendar-x"></i> Cerrar
                        </button>
                        <form method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta entrada de roster permanentemente?')">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_roster" value="<?= $r['id_roster'] ?>">
                            <input type="hidden" name="id_equipo" value="<?= $id_equipo ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <tr class="collapse" id="cerrar<?= $r['id_roster'] ?>">
                    <td colspan="5" class="bg-card">
                        <form method="POST" class="d-flex gap-3 align-items-end p-2">
                            <input type="hidden" name="accion" value="cerrar">
                            <input type="hidden" name="id_roster" value="<?= $r['id_roster'] ?>">
                            <input type="hidden" name="id_equipo" value="<?= $id_equipo ?>">
                            <div>
                                <label class="form-label text-white small mb-1">Fecha de salida</label>
                                <input type="date" class="form-control form-control-sm bg-dark text-white border-secondary"
                                       name="fecha_fin" value="<?= date('Y-m-d') ?>">
                            </div>
                            <button type="submit" class="btn btn-sm btn-warning">
                                <i class="bi bi-check-lg"></i> Cerrar entrada
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted mb-3">No hay jugadores activos en este roster.</p>
        <?php endif; ?>

        <!-- Añadir jugador -->
        <hr class="border-secondary">
        <h6 class="text-white small mb-2"><i class="bi bi-plus-circle"></i> Añadir jugador</h6>
        <form method="POST" class="row g-2">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id_equipo" value="<?= $id_equipo ?>">
            <div class="col-md-4">
                <select class="form-select form-select-sm bg-dark text-white border-secondary"
                        name="id_jugador" required>
                    <option value="">-- Jugador --</option>
                    <?php while ($j = mysqli_fetch_assoc($jugadores_libres)): ?>
                    <option value="<?= $j['id_jugador'] ?>">
                        <?= htmlspecialchars($j['nickname']) ?>
                        <?= $j['pais'] ? '(' . htmlspecialchars($j['pais']) . ')' : '' ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control form-control-sm bg-dark text-white border-secondary"
                       name="fecha_inicio" required value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch mt-1">
                    <input class="form-check-input" type="checkbox" name="titular" id="titular" checked>
                    <label class="form-check-label text-white small" for="titular">Titular</label>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-accent btn-sm w-100">
                    <i class="bi bi-plus-lg"></i> Añadir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Historial -->
<?php if (!empty($roster_historial)): ?>
<div class="card bg-dark border-secondary">
    <div class="card-header border-secondary">
        <h6 class="mb-0 text-muted"><i class="bi bi-clock-history"></i> Historial reciente</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-sm mb-0">
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($roster_historial as $r): ?>
            <tr>
                <td class="text-muted"><?= htmlspecialchars($r['nickname']) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($r['fecha_inicio'])) ?></td>
                <td class="text-muted small"><?= date('d/m/Y', strtotime($r['fecha_fin'])) ?></td>
                <td>
                    <span class="badge <?= $r['titular'] ? 'bg-success' : 'bg-secondary' ?> bg-opacity-50">
                        <?= $r['titular'] ? 'Titular' : 'Suplente' ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
