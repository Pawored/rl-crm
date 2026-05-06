<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$error = '';
$id_partido = intval($_GET['partido'] ?? $_POST['id_partido'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'guardar') {
    $id_partido = intval($_POST['id_partido'] ?? 0);
    $stats      = $_POST['stats'] ?? [];

    foreach ($stats as $id_jugador => $s) {
        $id_jugador  = intval($id_jugador);
        $goles       = intval($s['goles'] ?? 0);
        $asistencias = intval($s['asistencias'] ?? 0);
        $salvadas    = intval($s['salvadas'] ?? 0);
        $tiros       = intval($s['tiros'] ?? 0);
        $mvp         = isset($s['mvp']) ? 1 : 0;

        $check = mysqli_query($conexion,
            "SELECT id_estadistica FROM ESTADISTICAS_JUGADOR
             WHERE id_jugador=$id_jugador AND id_partido=$id_partido"
        );
        if (mysqli_num_rows($check) > 0) {
            $row = mysqli_fetch_assoc($check);
            mysqli_query($conexion,
                "UPDATE ESTADISTICAS_JUGADOR SET
                 goles=$goles, asistencias=$asistencias, salvadas=$salvadas,
                 tiros=$tiros, mvp=$mvp
                 WHERE id_estadistica={$row['id_estadistica']}"
            );
        } else {
            mysqli_query($conexion,
                "INSERT INTO ESTADISTICAS_JUGADOR
                 (id_jugador, id_partido, goles, asistencias, salvadas, tiros, mvp)
                 VALUES ($id_jugador, $id_partido, $goles, $asistencias, $salvadas, $tiros, $mvp)"
            );
        }
    }

    $_SESSION['mensaje_exito'] = "Estadísticas guardadas correctamente.";
    header("Location: /RLCS/CRM/pages/admin/estadisticas/entrada.php?partido=$id_partido");
    exit();
}

$partidos = mysqli_query($conexion,
    "SELECT p.id_partido, e1.nombre AS eq1, e2.nombre AS eq2,
            p.fecha_hora, t.nombre AS torneo
     FROM PARTIDO p
     JOIN EQUIPO e1 ON e1.id_equipo = p.id_equipo1
     JOIN EQUIPO e2 ON e2.id_equipo = p.id_equipo2
     JOIN TORNEO t ON t.id_torneo = p.id_torneo
     ORDER BY p.fecha_hora DESC
     LIMIT 100"
);

$partido_info = null;
$jugadores_partido = [];

if ($id_partido > 0) {
    $res_info = mysqli_query($conexion,
        "SELECT p.*, e1.nombre AS eq1, e2.nombre AS eq2,
                e1.id_equipo AS id_eq1, e2.id_equipo AS id_eq2,
                t.nombre AS torneo
         FROM PARTIDO p
         JOIN EQUIPO e1 ON e1.id_equipo = p.id_equipo1
         JOIN EQUIPO e2 ON e2.id_equipo = p.id_equipo2
         JOIN TORNEO t ON t.id_torneo = p.id_torneo
         WHERE p.id_partido = $id_partido"
    );
    $partido_info = mysqli_fetch_assoc($res_info);

    if ($partido_info) {
        foreach ([$partido_info['id_eq1'], $partido_info['id_eq2']] as $id_eq) {
            $res_j = mysqli_query($conexion,
                "SELECT j.id_jugador, j.nickname, j.pais,
                        COALESCE(ej.goles,0) AS goles,
                        COALESCE(ej.asistencias,0) AS asistencias,
                        COALESCE(ej.salvadas,0) AS salvadas,
                        COALESCE(ej.tiros,0) AS tiros,
                        COALESCE(ej.mvp,0) AS mvp,
                        e.nombre AS equipo
                 FROM ROSTER r
                 JOIN JUGADOR j ON j.id_jugador = r.id_jugador
                 JOIN EQUIPO e ON e.id_equipo = r.id_equipo
                 LEFT JOIN ESTADISTICAS_JUGADOR ej
                       ON ej.id_jugador = j.id_jugador AND ej.id_partido = $id_partido
                 WHERE r.id_equipo = $id_eq AND r.fecha_fin IS NULL
                 ORDER BY r.titular DESC, j.nickname"
            );
            $jugadores_partido[$id_eq] = [];
            while ($j = mysqli_fetch_assoc($res_j)) {
                $jugadores_partido[$id_eq][] = $j;
            }
        }
    }
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-graph-up"></i> Estadísticas de Jugadores</h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Selector de partido -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label text-white small">Seleccionar Partido</label>
                <select name="partido" class="form-select bg-dark text-white border-secondary">
                    <option value="0">-- Seleccionar partido --</option>
                    <?php while ($p = mysqli_fetch_assoc($partidos)): ?>
                    <option value="<?= $p['id_partido'] ?>"
                            <?= $id_partido == $p['id_partido'] ? 'selected' : '' ?>>
                        <?= date('d/m/Y', strtotime($p['fecha_hora'])) ?> —
                        <?= htmlspecialchars($p['eq1']) ?> vs <?= htmlspecialchars($p['eq2']) ?>
                        (<?= htmlspecialchars($p['torneo']) ?>)
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

<?php if ($id_partido > 0 && $partido_info): ?>
<div class="alert alert-dark border-secondary mb-4">
    <strong class="text-white"><?= htmlspecialchars($partido_info['eq1']) ?></strong>
    <span class="text-muted mx-2">vs</span>
    <strong class="text-white"><?= htmlspecialchars($partido_info['eq2']) ?></strong>
    <span class="text-muted ms-3 small">
        <?= date('d/m/Y H:i', strtotime($partido_info['fecha_hora'])) ?> ·
        <?= htmlspecialchars($partido_info['torneo']) ?> ·
        <?= htmlspecialchars($partido_info['formato']) ?>
    </span>
</div>

<form method="POST">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id_partido" value="<?= $id_partido ?>">

    <div class="row g-4">
    <?php foreach ([$partido_info['id_eq1'], $partido_info['id_eq2']] as $i => $id_eq):
        $jugadores = $jugadores_partido[$id_eq] ?? [];
        $nombre_equipo = ($i === 0) ? $partido_info['eq1'] : $partido_info['eq2'];
    ?>
    <div class="col-md-6">
        <div class="card bg-dark border-secondary h-100">
            <div class="card-header border-secondary">
                <h6 class="mb-0 text-accent">
                    <i class="bi bi-people-fill"></i> <?= htmlspecialchars($nombre_equipo) ?>
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($jugadores)): ?>
                <?php foreach ($jugadores as $j): ?>
                <div class="mb-3 pb-3 border-bottom border-secondary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="text-white"><?= htmlspecialchars($j['nickname']) ?></strong>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox"
                                   name="stats[<?= $j['id_jugador'] ?>][mvp]"
                                   id="mvp<?= $j['id_jugador'] ?>"
                                   <?= $j['mvp'] ? 'checked' : '' ?>>
                            <label class="form-check-label text-warning small" for="mvp<?= $j['id_jugador'] ?>">
                                <i class="bi bi-star-fill"></i> MVP
                            </label>
                        </div>
                    </div>
                    <div class="row g-2">
                        <?php
                        $campos = [
                            'goles'       => ['label' => 'Goles',    'icon' => 'dribbble'],
                            'asistencias' => ['label' => 'Asist.',   'icon' => 'arrow-up-right'],
                            'salvadas'    => ['label' => 'Salvadas', 'icon' => 'shield'],
                            'tiros'       => ['label' => 'Tiros',    'icon' => 'arrow-right-circle'],
                        ];
                        foreach ($campos as $campo => $meta):
                        ?>
                        <div class="col-6">
                            <label class="form-label text-muted small mb-1">
                                <i class="bi bi-<?= $meta['icon'] ?>"></i> <?= $meta['label'] ?>
                            </label>
                            <input type="number"
                                   class="form-control form-control-sm bg-dark text-white border-secondary"
                                   name="stats[<?= $j['id_jugador'] ?>][<?= $campo ?>]"
                                   min="0" value="<?= $j[$campo] ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small">No hay jugadores en el roster activo de este equipo.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-accent">
            <i class="bi bi-check-lg"></i> Guardar Estadísticas
        </button>
    </div>
</form>

<?php elseif ($id_partido > 0): ?>
    <div class="alert alert-warning">Partido no encontrado.</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
