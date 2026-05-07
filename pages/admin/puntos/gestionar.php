<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';
require_once __DIR__ . '/../../../includes/auditoria.php';

requiereRol('admin');

$id_temporada = intval($_GET['temporada'] ?? $_POST['id_temporada'] ?? 0);

// === CALCULAR AUTOMÁTICAMENTE DESDE PARTICIPACION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'calcular_auto') {
    $id_temporada = intval($_POST['id_temporada'] ?? 0);
    if ($id_temporada > 0) {
        // Agregar puntos por equipo y tipo de torneo
        $sql_agg = "SELECT p.id_equipo,
                           SUM(CASE WHEN t.tipo = 'regional' THEN p.puntos_ganados ELSE 0 END) AS reg,
                           SUM(CASE WHEN t.tipo = 'major'    THEN p.puntos_ganados ELSE 0 END) AS maj,
                           SUM(p.puntos_ganados) AS total
                    FROM PARTICIPACION p
                    INNER JOIN TORNEO t ON t.id_torneo = p.id_torneo
                    WHERE t.id_temporada = $id_temporada
                    GROUP BY p.id_equipo";
        $res_agg = mysqli_query($conexion, $sql_agg);
        $actualizados = 0;
        while ($row = mysqli_fetch_assoc($res_agg)) {
            $eid = intval($row['id_equipo']);
            $reg = intval($row['reg']);
            $maj = intval($row['maj']);
            $tot = intval($row['total']);

            $check = mysqli_query($conexion,
                "SELECT id_puntos FROM PUNTOS_RLCS
                 WHERE id_equipo=$eid AND id_temporada=$id_temporada");
            if (mysqli_num_rows($check) > 0) {
                $pid = mysqli_fetch_assoc($check)['id_puntos'];
                mysqli_query($conexion,
                    "UPDATE PUNTOS_RLCS SET puntos_regionals=$reg, puntos_majors=$maj,
                     puntos_totales=$tot WHERE id_puntos=$pid");
            } else {
                mysqli_query($conexion,
                    "INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales)
                     VALUES ($eid, $id_temporada, $reg, $maj, $tot)");
            }
            $actualizados++;
        }
        registrarAuditoria($conexion, 'CALCULAR', 'PUNTOS_RLCS', $id_temporada,
            "Auto-cálculo temporada $id_temporada: $actualizados equipos");
        $_SESSION['mensaje_exito'] = "Puntos recalculados desde PARTICIPACION: $actualizados equipos actualizados.";
    }
    header("Location: /RLCS/CRM/pages/admin/puntos/gestionar.php?temporada=$id_temporada");
    exit();
}

// === GUARDAR MANUAL ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'guardar') {
    $id_temporada = intval($_POST['id_temporada'] ?? 0);
    $filas = $_POST['filas'] ?? [];

    foreach ($filas as $id_equipo => $vals) {
        $id_equipo   = intval($id_equipo);
        $regionals   = intval($vals['regionals'] ?? 0);
        $majors      = intval($vals['majors'] ?? 0);
        $totales     = $regionals + $majors;

        $check = mysqli_query($conexion,
            "SELECT id_puntos FROM PUNTOS_RLCS
             WHERE id_equipo=$id_equipo AND id_temporada=$id_temporada"
        );
        if (mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            mysqli_query($conexion,
                "UPDATE PUNTOS_RLCS SET
                 puntos_regionals=$regionals, puntos_majors=$majors, puntos_totales=$totales
                 WHERE id_puntos={$row['id_puntos']}"
            );
            registrarAuditoria($conexion, 'UPDATE', 'PUNTOS_RLCS', $row['id_puntos'],
                "Equipo $id_equipo temp $id_temporada: reg=$regionals maj=$majors");
        } else {
            mysqli_query($conexion,
                "INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales)
                 VALUES ($id_equipo, $id_temporada, $regionals, $majors, $totales)"
            );
            registrarAuditoria($conexion, 'INSERT', 'PUNTOS_RLCS', null,
                "Equipo $id_equipo temp $id_temporada: reg=$regionals maj=$majors");
        }
    }

    $_SESSION['mensaje_exito'] = "Puntos actualizados correctamente.";
    header("Location: /RLCS/CRM/pages/admin/puntos/gestionar.php?temporada=$id_temporada");
    exit();
}

$temporadas = mysqli_query($conexion, "SELECT * FROM TEMPORADA ORDER BY anio DESC");

$datos = [];
$temporada_info = null;
if ($id_temporada > 0) {
    $res_info = mysqli_query($conexion, "SELECT * FROM TEMPORADA WHERE id_temporada=$id_temporada");
    $temporada_info = mysqli_fetch_assoc($res_info);

    $res = mysqli_query($conexion,
        "SELECT e.id_equipo, e.nombre, e.tag, r.siglas,
                COALESCE(p.puntos_regionals, 0) AS regionals,
                COALESCE(p.puntos_majors, 0)    AS majors,
                COALESCE(p.puntos_totales, 0)   AS totales
         FROM EQUIPO e
         JOIN REGION r ON r.id_region = e.id_region
         LEFT JOIN PUNTOS_RLCS p ON p.id_equipo = e.id_equipo AND p.id_temporada = $id_temporada
         WHERE e.activo = 1
         ORDER BY r.nombre, e.nombre"
    );
    while ($row = mysqli_fetch_assoc($res)) {
        $datos[] = $row;
    }
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-bar-chart"></i> Puntos RLCS</h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<!-- Selector temporada -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1" style="max-width:300px;">
                <label class="form-label text-white small">Temporada</label>
                <select name="temporada" class="form-select bg-dark text-white border-secondary">
                    <option value="0">-- Seleccionar temporada --</option>
                    <?php while ($t = mysqli_fetch_assoc($temporadas)): ?>
                    <option value="<?= $t['id_temporada'] ?>" <?= $id_temporada == $t['id_temporada'] ? 'selected' : '' ?>>
                        <?= $t['anio'] ?> (<?= date('d/m/Y', strtotime($t['fecha_inicio'])) ?> – <?= date('d/m/Y', strtotime($t['fecha_fin'])) ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-info">
                <i class="bi bi-funnel"></i> Ver
            </button>
        </form>
    </div>
</div>

<?php if ($id_temporada > 0 && !empty($datos)): ?>

<!-- Botón Auto-calcular -->
<div class="alert alert-secondary border-secondary d-flex align-items-center justify-content-between mb-4">
    <div>
        <i class="bi bi-calculator text-accent"></i>
        <strong class="text-white ms-1">Auto-calcular desde Participación</strong>
        <span class="text-muted ms-2 small">
            Suma los puntos ya registrados en PARTICIPACION por tipo de torneo (regional/major/mundial).
        </span>
    </div>
    <form method="POST" class="d-inline ms-3">
        <input type="hidden" name="accion" value="calcular_auto">
        <input type="hidden" name="id_temporada" value="<?= $id_temporada ?>">
        <button type="submit" class="btn btn-outline-info btn-sm text-nowrap"
                onclick="return confirm('¿Recalcular todos los puntos de la temporada <?= $temporada_info['anio'] ?> desde PARTICIPACION?')">
            <i class="bi bi-arrow-repeat"></i> Recalcular automáticamente
        </button>
    </form>
</div>

<form method="POST">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id_temporada" value="<?= $id_temporada ?>">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-white mb-0">
            Temporada <?= $temporada_info['anio'] ?>
            <span class="badge bg-secondary ms-2"><?= count($datos) ?> equipos</span>
        </h6>
        <button type="submit" class="btn btn-accent btn-sm">
            <i class="bi bi-check-lg"></i> Guardar Todo
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>Región</th>
                    <th>Equipo</th>
                    <th style="width:150px">Regionals</th>
                    <th style="width:150px">Majors</th>
                    <th style="width:130px">Total (auto)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $region_anterior = '';
            foreach ($datos as $d):
                if ($d['siglas'] !== $region_anterior) {
                    $region_anterior = $d['siglas'];
                    echo '<tr class="table-secondary"><td colspan="5" class="py-1 px-3">
                          <small class="fw-bold text-muted">' . htmlspecialchars($d['siglas']) . '</small>
                          </td></tr>';
                }
            ?>
            <tr>
                <td class="text-muted small"><?= htmlspecialchars($d['siglas']) ?></td>
                <td>
                    <strong class="text-white"><?= htmlspecialchars($d['nombre']) ?></strong>
                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($d['tag']) ?></span>
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm bg-dark text-white border-secondary puntos-input"
                           name="filas[<?= $d['id_equipo'] ?>][regionals]"
                           data-row="<?= $d['id_equipo'] ?>"
                           min="0" value="<?= $d['regionals'] ?>">
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm bg-dark text-white border-secondary puntos-input"
                           name="filas[<?= $d['id_equipo'] ?>][majors]"
                           data-row="<?= $d['id_equipo'] ?>"
                           min="0" value="<?= $d['majors'] ?>">
                </td>
                <td>
                    <span class="text-accent fw-bold" id="total-<?= $d['id_equipo'] ?>">
                        <?= $d['totales'] ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-accent">
            <i class="bi bi-check-lg"></i> Guardar Todo
        </button>
    </div>
</form>

<script>
document.querySelectorAll('.puntos-input').forEach(input => {
    input.addEventListener('input', () => {
        const row = input.dataset.row;
        const inputs = document.querySelectorAll(`.puntos-input[data-row="${row}"]`);
        let total = 0;
        inputs.forEach(i => total += parseInt(i.value) || 0);
        document.getElementById(`total-${row}`).textContent = total;
    });
});
</script>

<?php elseif ($id_temporada > 0): ?>
    <div class="alert alert-warning">No hay equipos activos para mostrar.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>


$id_temporada = intval($_GET['temporada'] ?? $_POST['id_temporada'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'guardar') {
    $id_temporada = intval($_POST['id_temporada'] ?? 0);
    $filas = $_POST['filas'] ?? [];

    foreach ($filas as $id_equipo => $vals) {
        $id_equipo   = intval($id_equipo);
        $regionals   = intval($vals['regionals'] ?? 0);
        $majors      = intval($vals['majors'] ?? 0);
        $totales     = $regionals + $majors;

        $check = mysqli_query($conexion,
            "SELECT id_puntos FROM PUNTOS_RLCS
             WHERE id_equipo=$id_equipo AND id_temporada=$id_temporada"
        );
        if (mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            mysqli_query($conexion,
                "UPDATE PUNTOS_RLCS SET
                 puntos_regionals=$regionals, puntos_majors=$majors, puntos_totales=$totales
                 WHERE id_puntos={$row['id_puntos']}"
            );
        } else {
            mysqli_query($conexion,
                "INSERT INTO PUNTOS_RLCS (id_equipo, id_temporada, puntos_regionals, puntos_majors, puntos_totales)
                 VALUES ($id_equipo, $id_temporada, $regionals, $majors, $totales)"
            );
        }
    }

    $_SESSION['mensaje_exito'] = "Puntos actualizados correctamente.";
    header("Location: /RLCS/CRM/pages/admin/puntos/gestionar.php?temporada=$id_temporada");
    exit();
}

$temporadas = mysqli_query($conexion, "SELECT * FROM TEMPORADA ORDER BY anio DESC");

$datos = [];
$temporada_info = null;
if ($id_temporada > 0) {
    $res_info = mysqli_query($conexion, "SELECT * FROM TEMPORADA WHERE id_temporada=$id_temporada");
    $temporada_info = mysqli_fetch_assoc($res_info);

    $res = mysqli_query($conexion,
        "SELECT e.id_equipo, e.nombre, e.tag, r.siglas,
                COALESCE(p.puntos_regionals, 0) AS regionals,
                COALESCE(p.puntos_majors, 0)    AS majors,
                COALESCE(p.puntos_totales, 0)   AS totales
         FROM EQUIPO e
         JOIN REGION r ON r.id_region = e.id_region
         LEFT JOIN PUNTOS_RLCS p ON p.id_equipo = e.id_equipo AND p.id_temporada = $id_temporada
         WHERE e.activo = 1
         ORDER BY r.nombre, e.nombre"
    );
    while ($row = mysqli_fetch_assoc($res)) {
        $datos[] = $row;
    }
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-bar-chart"></i> Puntos RLCS</h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<!-- Selector temporada -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1" style="max-width:300px;">
                <label class="form-label text-white small">Temporada</label>
                <select name="temporada" class="form-select bg-dark text-white border-secondary">
                    <option value="0">-- Seleccionar temporada --</option>
                    <?php while ($t = mysqli_fetch_assoc($temporadas)): ?>
                    <option value="<?= $t['id_temporada'] ?>" <?= $id_temporada == $t['id_temporada'] ? 'selected' : '' ?>>
                        <?= $t['anio'] ?> (<?= date('d/m/Y', strtotime($t['fecha_inicio'])) ?> – <?= date('d/m/Y', strtotime($t['fecha_fin'])) ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-info">
                <i class="bi bi-funnel"></i> Ver
            </button>
        </form>
    </div>
</div>

<?php if ($id_temporada > 0 && !empty($datos)): ?>
<form method="POST">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id_temporada" value="<?= $id_temporada ?>">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-white mb-0">
            Temporada <?= $temporada_info['anio'] ?>
            <span class="badge bg-secondary ms-2"><?= count($datos) ?> equipos</span>
        </h6>
        <button type="submit" class="btn btn-accent btn-sm">
            <i class="bi bi-check-lg"></i> Guardar Todo
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>Región</th>
                    <th>Equipo</th>
                    <th style="width:150px">Regionals</th>
                    <th style="width:150px">Majors</th>
                    <th style="width:130px">Total (auto)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $region_anterior = '';
            foreach ($datos as $d):
                if ($d['siglas'] !== $region_anterior) {
                    $region_anterior = $d['siglas'];
                    echo '<tr class="table-secondary"><td colspan="5" class="py-1 px-3">
                          <small class="fw-bold text-muted">' . htmlspecialchars($d['siglas']) . '</small>
                          </td></tr>';
                }
            ?>
            <tr>
                <td class="text-muted small"><?= htmlspecialchars($d['siglas']) ?></td>
                <td>
                    <strong class="text-white"><?= htmlspecialchars($d['nombre']) ?></strong>
                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($d['tag']) ?></span>
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm bg-dark text-white border-secondary puntos-input"
                           name="filas[<?= $d['id_equipo'] ?>][regionals]"
                           data-row="<?= $d['id_equipo'] ?>"
                           min="0" value="<?= $d['regionals'] ?>">
                </td>
                <td>
                    <input type="number"
                           class="form-control form-control-sm bg-dark text-white border-secondary puntos-input"
                           name="filas[<?= $d['id_equipo'] ?>][majors]"
                           data-row="<?= $d['id_equipo'] ?>"
                           min="0" value="<?= $d['majors'] ?>">
                </td>
                <td>
                    <span class="text-accent fw-bold" id="total-<?= $d['id_equipo'] ?>">
                        <?= $d['totales'] ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-accent">
            <i class="bi bi-check-lg"></i> Guardar Todo
        </button>
    </div>
</form>

<script>
document.querySelectorAll('.puntos-input').forEach(input => {
    input.addEventListener('input', () => {
        const row = input.dataset.row;
        const inputs = document.querySelectorAll(`.puntos-input[data-row="${row}"]`);
        let total = 0;
        inputs.forEach(i => total += parseInt(i.value) || 0);
        document.getElementById(`total-${row}`).textContent = total;
    });
});
</script>

<?php elseif ($id_temporada > 0): ?>
    <div class="alert alert-warning">No hay equipos activos para mostrar.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
