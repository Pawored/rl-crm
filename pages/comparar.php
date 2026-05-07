<?php
/**
 * COMPARAR — Comparador H2H de equipos o jugadores
 * Modo equipos: historial de partidos directos + marcador de victorias
 * Modo jugadores: estadísticas agregadas lado a lado
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/sesion.php';

// === PARÁMETROS ===
$tipo = in_array($_GET['tipo'] ?? '', ['equipos', 'jugadores']) ? $_GET['tipo'] : 'equipos';
$id1  = intval($_GET['id1'] ?? 0);
$id2  = intval($_GET['id2'] ?? 0);

// === DATOS PARA SELECTORES ===
$todos_equipos  = mysqli_query($conexion,
    "SELECT id_equipo, nombre, tag FROM EQUIPO ORDER BY nombre ASC");
$todos_jugadores = mysqli_query($conexion,
    "SELECT id_jugador, nickname FROM JUGADOR WHERE activo = 1 ORDER BY nickname ASC");

// === RESULTADO COMPARACIÓN ===
$entidad1 = $entidad2 = null;
$partidos_h2h = [];
$v1 = $v2 = $pendientes = 0;
$stats1 = $stats2 = null;

if ($id1 > 0 && $id2 > 0 && $id1 !== $id2) {

    if ($tipo === 'equipos') {
        // Datos de los dos equipos
        $entidad1 = mysqli_fetch_assoc(mysqli_query($conexion,
            "SELECT id_equipo AS id, nombre, tag FROM EQUIPO WHERE id_equipo = $id1"));
        $entidad2 = mysqli_fetch_assoc(mysqli_query($conexion,
            "SELECT id_equipo AS id, nombre, tag FROM EQUIPO WHERE id_equipo = $id2"));

        // Partidos H2H
        $sql_h2h = "SELECT p.id_partido, p.fecha_hora, p.formato, p.id_ganador,
                           e1.tag AS tag1, e1.id_equipo AS id_eq1,
                           e2.tag AS tag2, e2.id_equipo AS id_eq2,
                           t.nombre AS torneo
                    FROM PARTIDO p
                    INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
                    INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
                    LEFT JOIN TORNEO t ON p.id_torneo = t.id_torneo
                    WHERE (p.id_equipo1 = $id1 AND p.id_equipo2 = $id2)
                       OR (p.id_equipo1 = $id2 AND p.id_equipo2 = $id1)
                    ORDER BY p.fecha_hora DESC";
        $res_h2h = mysqli_query($conexion, $sql_h2h);
        while ($row = mysqli_fetch_assoc($res_h2h)) {
            $partidos_h2h[] = $row;
            if ($row['id_ganador'] == $id1) $v1++;
            elseif ($row['id_ganador'] == $id2) $v2++;
            else $pendientes++;
        }

    } else {
        // Datos de los dos jugadores
        $entidad1 = mysqli_fetch_assoc(mysqli_query($conexion,
            "SELECT id_jugador AS id, nickname AS nombre, '' AS tag FROM JUGADOR WHERE id_jugador = $id1"));
        $entidad2 = mysqli_fetch_assoc(mysqli_query($conexion,
            "SELECT id_jugador AS id, nickname AS nombre, '' AS tag FROM JUGADOR WHERE id_jugador = $id2"));

        // Stats agregadas
        $fn_stats = function($id) use ($conexion) {
            return mysqli_fetch_assoc(mysqli_query($conexion,
                "SELECT COALESCE(SUM(goles),0) goles, COALESCE(SUM(asistencias),0) asistencias,
                        COALESCE(SUM(salvadas),0) salvadas, COALESCE(SUM(tiros),0) tiros,
                        COALESCE(SUM(mvp),0) mvps, COUNT(DISTINCT id_partido) partidos
                 FROM ESTADISTICAS_JUGADOR WHERE id_jugador = $id"));
        };
        $stats1 = $fn_stats($id1);
        $stats2 = $fn_stats($id2);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-arrows-angle-contract"></i> Comparador
    </h2>
</div>

<!-- ========== FORMULARIO SELECTOR ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body">
        <form method="GET" action="/RLCS/CRM/pages/comparar.php">
            <!-- Selector de modo -->
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="tipo" id="tipoEquipos"
                           value="equipos" <?= ($tipo === 'equipos') ? 'checked' : '' ?>>
                    <label class="btn btn-outline-info" for="tipoEquipos">
                        <i class="bi bi-people-fill"></i> Equipos
                    </label>
                    <input type="radio" class="btn-check" name="tipo" id="tipoJugadores"
                           value="jugadores" <?= ($tipo === 'jugadores') ? 'checked' : '' ?>>
                    <label class="btn btn-outline-info" for="tipoJugadores">
                        <i class="bi bi-person-badge"></i> Jugadores
                    </label>
                </div>
            </div>

            <div class="row g-3 align-items-end">
                <!-- Selector entidad 1 -->
                <div class="col-md-5">
                    <label class="form-label text-muted">
                        <span id="labelA">Equipo / Jugador A</span>
                    </label>
                    <!-- Equipos -->
                    <select name="id1" class="form-select bg-dark text-white border-secondary selector-equipos"
                            style="display:<?= ($tipo === 'equipos') ? 'block' : 'none' ?>">
                        <option value="">-- Selecciona --</option>
                        <?php
                        mysqli_data_seek($todos_equipos, 0);
                        while ($e = mysqli_fetch_assoc($todos_equipos)):
                        ?>
                            <option value="<?= $e['id_equipo'] ?>"
                                    <?= ($id1 == $e['id_equipo'] && $tipo === 'equipos') ? 'selected' : '' ?>>
                                [<?= htmlspecialchars($e['tag']) ?>] <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <!-- Jugadores -->
                    <select name="id1" class="form-select bg-dark text-white border-secondary selector-jugadores"
                            style="display:<?= ($tipo === 'jugadores') ? 'block' : 'none' ?>">
                        <option value="">-- Selecciona --</option>
                        <?php
                        mysqli_data_seek($todos_jugadores, 0);
                        while ($j = mysqli_fetch_assoc($todos_jugadores)):
                        ?>
                            <option value="<?= $j['id_jugador'] ?>"
                                    <?= ($id1 == $j['id_jugador'] && $tipo === 'jugadores') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nickname']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-1 text-center">
                    <span class="text-accent fw-bold fs-4">VS</span>
                </div>

                <!-- Selector entidad 2 -->
                <div class="col-md-5">
                    <label class="form-label text-muted">
                        <span>Equipo / Jugador B</span>
                    </label>
                    <!-- Equipos -->
                    <select name="id2" class="form-select bg-dark text-white border-secondary selector-equipos"
                            style="display:<?= ($tipo === 'equipos') ? 'block' : 'none' ?>">
                        <option value="">-- Selecciona --</option>
                        <?php
                        mysqli_data_seek($todos_equipos, 0);
                        while ($e = mysqli_fetch_assoc($todos_equipos)):
                        ?>
                            <option value="<?= $e['id_equipo'] ?>"
                                    <?= ($id2 == $e['id_equipo'] && $tipo === 'equipos') ? 'selected' : '' ?>>
                                [<?= htmlspecialchars($e['tag']) ?>] <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <!-- Jugadores -->
                    <select name="id2" class="form-select bg-dark text-white border-secondary selector-jugadores"
                            style="display:<?= ($tipo === 'jugadores') ? 'block' : 'none' ?>">
                        <option value="">-- Selecciona --</option>
                        <?php
                        mysqli_data_seek($todos_jugadores, 0);
                        while ($j = mysqli_fetch_assoc($todos_jugadores)):
                        ?>
                            <option value="<?= $j['id_jugador'] ?>"
                                    <?= ($id2 == $j['id_jugador'] && $tipo === 'jugadores') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nickname']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-accent w-100">
                        <i class="bi bi-search"></i> Comparar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($entidad1 && $entidad2): ?>

<?php if ($tipo === 'equipos'): ?>
<!-- ========== RESULTADO EQUIPOS ========== -->

<!-- Marcador H2H -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-body text-center py-4">
        <div class="row align-items-center">
            <div class="col-4">
                <h3 class="text-white fw-bold">
                    <?= htmlspecialchars($entidad1['nombre']) ?>
                </h3>
                <span class="badge bg-secondary fs-6"><?= htmlspecialchars($entidad1['tag']) ?></span>
                <div class="mt-3">
                    <span class="display-4 fw-bold text-success"><?= $v1 ?></span>
                    <div class="text-muted small mt-1">victorias</div>
                </div>
            </div>
            <div class="col-4">
                <div class="text-muted mb-2">H2H</div>
                <div class="display-6 fw-bold text-accent">
                    <?= count($partidos_h2h) ?>
                </div>
                <div class="text-muted small">partidos</div>
                <?php if ($pendientes > 0): ?>
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark"><?= $pendientes ?> pendiente(s)</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-4">
                <h3 class="text-white fw-bold">
                    <?= htmlspecialchars($entidad2['nombre']) ?>
                </h3>
                <span class="badge bg-secondary fs-6"><?= htmlspecialchars($entidad2['tag']) ?></span>
                <div class="mt-3">
                    <span class="display-4 fw-bold text-danger"><?= $v2 ?></span>
                    <div class="text-muted small mt-1">victorias</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de enfrentamientos -->
<?php if (!empty($partidos_h2h)): ?>
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-joystick"></i> Historial de Enfrentamientos
        </h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Torneo</th>
                    <th class="text-center">Resultado</th>
                    <th>Formato</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidos_h2h as $p): ?>
                <tr>
                    <td class="text-muted">
                        <?= $p['fecha_hora'] ? date('d/m/Y', strtotime($p['fecha_hora'])) : 'N/A' ?>
                    </td>
                    <td><small><?= htmlspecialchars($p['torneo'] ?? 'N/A') ?></small></td>
                    <td class="text-center">
                        <?php if ($p['id_ganador']): ?>
                            <span class="<?= ($p['id_ganador'] == $id1) ? 'text-success fw-bold' : 'text-muted' ?>">
                                <?= htmlspecialchars($p['tag1']) ?>
                            </span>
                            <span class="text-muted mx-1">vs</span>
                            <span class="<?= ($p['id_ganador'] == $id2) ? 'text-success fw-bold' : 'text-muted' ?>">
                                <?= htmlspecialchars($p['tag2']) ?>
                            </span>
                            <i class="bi bi-trophy-fill text-warning ms-1" title="Ganador"></i>
                        <?php else: ?>
                            <span class="text-muted"><?= htmlspecialchars($p['tag1']) ?> vs <?= htmlspecialchars($p['tag2']) ?></span>
                            <span class="badge bg-warning text-dark ms-1">Pendiente</span>
                        <?php endif; ?>
                    </td>
                    <td><small class="text-muted"><?= htmlspecialchars($p['formato'] ?? '') ?></small></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Estos equipos no tienen enfrentamientos directos registrados.
</div>
<?php endif; ?>

<?php else: ?>
<!-- ========== RESULTADO JUGADORES ========== -->
<?php
$campos = [
    'partidos'    => ['label' => 'Partidos', 'icon' => 'bi-joystick'],
    'goles'       => ['label' => 'Goles',    'icon' => 'bi-circle-fill text-accent'],
    'asistencias' => ['label' => 'Asistencias', 'icon' => 'bi-arrow-up-right-circle text-info'],
    'salvadas'    => ['label' => 'Salvadas', 'icon' => 'bi-shield-fill text-success'],
    'tiros'       => ['label' => 'Tiros',    'icon' => 'bi-bullseye text-warning'],
    'mvps'        => ['label' => 'MVPs',     'icon' => 'bi-star-fill text-danger'],
];
?>
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <div class="row text-center">
            <div class="col-5">
                <h5 class="text-white mb-0"><?= htmlspecialchars($entidad1['nombre']) ?></h5>
            </div>
            <div class="col-2"></div>
            <div class="col-5">
                <h5 class="text-white mb-0"><?= htmlspecialchars($entidad2['nombre']) ?></h5>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php foreach ($campos as $campo => $meta): ?>
        <?php
        $val1 = (int)($stats1[$campo] ?? 0);
        $val2 = (int)($stats2[$campo] ?? 0);
        $max  = max($val1, $val2, 1);
        $pct1 = round($val1 / $max * 100);
        $pct2 = round($val2 / $max * 100);
        ?>
        <div class="row align-items-center mb-3">
            <div class="col-5 text-end">
                <span class="fw-bold <?= ($val1 >= $val2) ? 'text-accent' : 'text-white' ?> me-2">
                    <?= $val1 ?>
                </span>
                <div class="progress flex-grow-1 d-inline-block" style="width:70%;height:8px;vertical-align:middle">
                    <div class="progress-bar bg-info" style="width:<?= $pct1 ?>%"></div>
                </div>
            </div>
            <div class="col-2 text-center">
                <small class="text-muted">
                    <i class="bi <?= $meta['icon'] ?>"></i>
                    <?= $meta['label'] ?>
                </small>
            </div>
            <div class="col-5 text-start">
                <div class="progress flex-grow-1 d-inline-block" style="width:70%;height:8px;vertical-align:middle">
                    <div class="progress-bar bg-accent" style="width:<?= $pct2 ?>;background:#00d4ff!important"></div>
                </div>
                <span class="fw-bold <?= ($val2 >= $val1) ? 'text-accent' : 'text-white' ?> ms-2">
                    <?= $val2 ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php elseif ($id1 > 0 && $id2 > 0 && $id1 === $id2): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> Selecciona dos entidades diferentes para comparar.
</div>
<?php endif; ?>

<script>
// Alternar selectores según modo elegido
document.querySelectorAll('input[name="tipo"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const esEquipos = this.value === 'equipos';
        document.querySelectorAll('.selector-equipos').forEach(function(el) {
            el.style.display = esEquipos ? 'block' : 'none';
            el.disabled = !esEquipos;
        });
        document.querySelectorAll('.selector-jugadores').forEach(function(el) {
            el.style.display = esEquipos ? 'none' : 'block';
            el.disabled = esEquipos;
        });
    });
    // Deshabilitar selects del modo no activo al cargar
    if (!radio.checked) {
        const sel = radio.value === 'equipos' ? '.selector-equipos' : '.selector-jugadores';
        document.querySelectorAll(sel).forEach(function(el) { el.disabled = true; });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
