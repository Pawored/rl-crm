<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';
require_once __DIR__ . '/../../../includes/db.php';

requiereRol('admin');

$id_torneo = intval($_GET['torneo'] ?? 0);

$torneos = db_fetch_all($conexion,
    "SELECT t.id_torneo, t.nombre, t.tipo, temp.anio
     FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
     ORDER BY temp.anio DESC, t.nombre");

$torneo_info = null;
$partidos    = [];
$rounds      = [];

if ($id_torneo > 0) {
    $torneo_info = db_fetch_one($conexion,
        "SELECT t.id_torneo, t.nombre, t.tipo, temp.anio
         FROM TORNEO t JOIN TEMPORADA temp ON temp.id_temporada = t.id_temporada
         WHERE t.id_torneo = ?", "i", $id_torneo);

    if ($torneo_info) {
        $raw = db_fetch_all($conexion,
            "SELECT p.id_partido, p.id_equipo1, p.id_equipo2, p.id_ganador,
                    p.fecha_hora, p.formato,
                    e1.nombre AS nombre1, e1.tag AS tag1,
                    e2.nombre AS nombre2, e2.tag AS tag2
             FROM PARTIDO p
             JOIN EQUIPO e1 ON e1.id_equipo = p.id_equipo1
             JOIN EQUIPO e2 ON e2.id_equipo = p.id_equipo2
             WHERE p.id_torneo = ?
             ORDER BY p.id_partido ASC", "i", $id_torneo);

        foreach ($raw as $p) {
            $juegos = db_fetch_all($conexion,
                "SELECT goles_equipo1, goles_equipo2 FROM JUEGO WHERE id_partido = ?",
                "i", $p['id_partido']);
            $wins1 = $wins2 = 0;
            foreach ($juegos as $j) {
                if ($j['goles_equipo1'] > $j['goles_equipo2']) $wins1++;
                else $wins2++;
            }
            $p['wins1']       = $wins1;
            $p['wins2']       = $wins2;
            $p['juego_count'] = count($juegos);
            $partidos[] = $p;
        }

        $n = count($partidos);
        if ($n >= 15) {
            $rounds = [
                ['label' => 'Ronda 1',          'matches' => array_slice($partidos, 0,  8)],
                ['label' => 'Cuartos de Final',  'matches' => array_slice($partidos, 8,  4)],
                ['label' => 'Semifinal',         'matches' => array_slice($partidos, 12, 2)],
                ['label' => 'Gran Final',        'matches' => array_slice($partidos, 14, 1)],
            ];
        } elseif ($n >= 7) {
            $rounds = [
                ['label' => 'Cuartos de Final', 'matches' => array_slice($partidos, 0, 4)],
                ['label' => 'Semifinal',        'matches' => array_slice($partidos, 4, 2)],
                ['label' => 'Gran Final',       'matches' => array_slice($partidos, 6, 1)],
            ];
        } elseif ($n >= 3) {
            $rounds = [
                ['label' => 'Semifinal',  'matches' => array_slice($partidos, 0, 2)],
                ['label' => 'Gran Final', 'matches' => array_slice($partidos, 2, 1)],
            ];
        } elseif ($n >= 1) {
            $rounds = [['label' => 'Final', 'matches' => $partidos]];
        }
    }
}

// Height: first round drives total; each subsequent round has 2× slot height
$BASE_SLOT   = 115;
$first_count = !empty($rounds) ? count($rounds[0]['matches']) : 0;
$bracket_h   = max($first_count, 1) * $BASE_SLOT;

$page_title = 'Bracket de Torneos';
require_once __DIR__ . '/../../../includes/header.php';
?>

<style>
.bracket-match-card { transition: border-color .2s; cursor: default; }
.bracket-match-card:hover { border-color: rgba(0,212,255,.5) !important; }
.team-win  { background: rgba(34,197,94,.12); }
.team-lose { opacity: .55; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-diagram-3-fill"></i> Bracket de Torneos</h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<!-- ── Selector ── -->
<div class="card bg-card border-secondary mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-3 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label text-white small mb-1">Seleccionar Torneo</label>
                <select name="torneo" class="form-select bg-dark text-white border-secondary">
                    <option value="0">-- Seleccionar torneo --</option>
                    <?php foreach ($torneos as $t): ?>
                    <option value="<?= $t['id_torneo'] ?>"
                            <?= $id_torneo == $t['id_torneo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?>
                        (<?= $t['anio'] ?>) — <?= ucfirst($t['tipo']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-accent">
                <i class="bi bi-diagram-3"></i> Ver Bracket
            </button>
        </form>
    </div>
</div>

<?php if ($id_torneo > 0 && $torneo_info): ?>

<!-- ── Info banner ── -->
<div class="card bg-card border-secondary mb-4">
    <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
        <i class="bi bi-trophy-fill text-accent fs-4"></i>
        <div>
            <span class="fw-bold text-white"><?= htmlspecialchars($torneo_info['nombre']) ?></span>
            <span class="badge bg-secondary ms-2"><?= $torneo_info['anio'] ?></span>
            <span class="badge bg-info ms-1"><?= ucfirst($torneo_info['tipo']) ?></span>
        </div>
        <span class="ms-auto text-muted small"><?= count($partidos) ?> partido(s)</span>
    </div>
</div>

<?php if (empty($rounds)): ?>
<div class="alert alert-secondary">
    <i class="bi bi-info-circle"></i> No hay partidos registrados para este torneo todavía.
    <a href="/RLCS/CRM/pages/admin/partidos/index.php" class="alert-link ms-2">Crear partidos</a>
</div>

<?php else: ?>

<!-- ── Bracket visual ── -->
<div class="card bg-card border-secondary mb-4">
    <div class="card-header border-secondary">
        <h5 class="mb-0 text-accent"><i class="bi bi-diagram-3"></i> Bracket Visual</h5>
    </div>
    <div class="card-body overflow-auto p-4">
        <div id="bracketWrapper"
             class="d-flex"
             style="position:relative; gap:0; min-height:<?= $bracket_h + 40 ?>px">

            <?php
            $round_count = count($rounds);
            foreach ($rounds as $ri => $round):
                $match_count = count($round['matches']);
                $slot_h      = $match_count > 0 ? intdiv($bracket_h, $match_count) : $bracket_h;
                $is_last     = ($ri === $round_count - 1);
            ?>

            <div class="bracket-col d-flex flex-column" style="min-width:230px; flex-shrink:0">

                <!-- Round label -->
                <div class="text-center" style="height:38px; display:flex; align-items:center; justify-content:center">
                    <span style="font-size:.68rem; font-weight:700; letter-spacing:1.5px;
                                 color:#00d4ff; text-transform:uppercase">
                        <?= htmlspecialchars($round['label']) ?>
                    </span>
                </div>

                <?php foreach ($round['matches'] as $m):
                    $decided = !empty($m['id_ganador']);
                    $won1    = $decided && $m['id_ganador'] == $m['id_equipo1'];
                    $won2    = $decided && $m['id_ganador'] == $m['id_equipo2'];
                    $nojueg  = $m['juego_count'] === 0;
                    $winner_tag = $won1 ? $m['tag1'] : ($won2 ? $m['tag2'] : '');
                ?>
                <div class="bracket-slot d-flex align-items-center justify-content-center"
                     style="height:<?= $slot_h ?>px; padding:6px 10px">
                    <div class="bracket-match-card w-100 rounded overflow-hidden border"
                         style="border-color:rgba(0,212,255,.18)!important; background:#0d0d1f; max-width:210px">

                        <!-- Team 1 -->
                        <div class="d-flex align-items-center justify-content-between px-2 py-1
                                    <?= $won1 ? 'team-win' : ($decided ? 'team-lose' : '') ?>"
                             style="border-left:3px solid #00d4ff; min-height:32px">
                            <span class="fw-bold text-white text-truncate"
                                  style="font-size:.78rem; max-width:140px">
                                <span class="text-muted me-1" style="font-size:.65rem">
                                    <?= htmlspecialchars($m['tag1']) ?>
                                </span>
                                <?= htmlspecialchars($m['nombre1']) ?>
                            </span>
                            <?php if (!$nojueg): ?>
                            <span class="badge ms-1 flex-shrink-0 <?= $won1 ? 'bg-success' : 'bg-secondary' ?>"
                                  style="min-width:22px"><?= $m['wins1'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div style="height:1px; background:rgba(255,255,255,.05)"></div>

                        <!-- Team 2 -->
                        <div class="d-flex align-items-center justify-content-between px-2 py-1
                                    <?= $won2 ? 'team-win' : ($decided ? 'team-lose' : '') ?>"
                             style="border-left:3px solid #ff9900; min-height:32px">
                            <span class="fw-bold text-white text-truncate"
                                  style="font-size:.78rem; max-width:140px">
                                <span class="text-muted me-1" style="font-size:.65rem">
                                    <?= htmlspecialchars($m['tag2']) ?>
                                </span>
                                <?= htmlspecialchars($m['nombre2']) ?>
                            </span>
                            <?php if (!$nojueg): ?>
                            <span class="badge ms-1 flex-shrink-0 <?= $won2 ? 'bg-success' : 'bg-secondary' ?>"
                                  style="min-width:22px"><?= $m['wins2'] ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div style="background:rgba(0,0,0,.4); height:17px; font-size:.6rem; color:#4a5070;
                                    display:flex; align-items:center; justify-content:center; gap:5px">
                            <span><?= htmlspecialchars($m['formato']) ?></span>
                            <?php if ($nojueg): ?>
                                <span style="color:#ffa500">· Pendiente</span>
                            <?php elseif ($winner_tag): ?>
                                <span style="color:#22c55e">· <?= htmlspecialchars($winner_tag) ?> wins</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (!$is_last): ?>
            <div style="width:60px; flex-shrink:0"></div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── Results table ── -->
<div class="card bg-card border-secondary mb-4">
    <div class="card-header border-secondary">
        <h5 class="mb-0 text-white"><i class="bi bi-list-ol"></i> Resultados</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0 align-middle small">
            <thead>
                <tr>
                    <th class="text-muted ps-3" style="width:28px">#</th>
                    <th>Enfrentamiento</th>
                    <th class="text-center">Marcador</th>
                    <th>Ganador</th>
                    <th class="text-muted">Formato</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($partidos as $i => $m):
                $decided = !empty($m['id_ganador']);
                $won1    = $decided && $m['id_ganador'] == $m['id_equipo1'];
                $won2    = $decided && $m['id_ganador'] == $m['id_equipo2'];
            ?>
            <tr>
                <td class="text-muted ps-3"><?= $i + 1 ?></td>
                <td>
                    <span class="<?= $won1 ? 'text-success fw-bold' : ($decided ? 'text-muted' : 'text-white') ?>">
                        <?= htmlspecialchars($m['tag1']) ?>
                    </span>
                    <span class="text-muted"> vs </span>
                    <span class="<?= $won2 ? 'text-success fw-bold' : ($decided ? 'text-muted' : 'text-white') ?>">
                        <?= htmlspecialchars($m['tag2']) ?>
                    </span>
                </td>
                <td class="text-center fw-bold">
                    <?php if ($m['juego_count'] > 0): ?>
                    <span class="<?= $won1 ? 'text-success' : 'text-muted' ?>"><?= $m['wins1'] ?></span>
                    <span class="text-muted"> – </span>
                    <span class="<?= $won2 ? 'text-success' : 'text-muted' ?>"><?= $m['wins2'] ?></span>
                    <?php else: ?>
                    <span class="text-warning">TBD</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($decided): ?>
                    <span class="text-success">
                        <i class="bi bi-trophy-fill"></i>
                        <?= htmlspecialchars($won1 ? $m['nombre1'] : $m['nombre2']) ?>
                    </span>
                    <?php else: ?>
                    <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td class="text-muted"><?= htmlspecialchars($m['formato']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; // empty rounds ?>

<?php elseif ($id_torneo > 0): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i> Torneo no encontrado.
</div>
<?php endif; ?>

<script>
(function () {
    var wrapper = document.getElementById('bracketWrapper');
    if (!wrapper) return;

    var ns  = 'http://www.w3.org/2000/svg';
    var svg = document.createElementNS(ns, 'svg');
    svg.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;' +
                        'pointer-events:none;overflow:visible;z-index:0;';
    wrapper.insertBefore(svg, wrapper.firstChild);

    var wr = wrapper.getBoundingClientRect();

    function rcx(el) { return el.getBoundingClientRect().right  - wr.left; }
    function lcx(el) { return el.getBoundingClientRect().left   - wr.left; }
    function mcy(el) { var r = el.getBoundingClientRect(); return r.top + r.height / 2 - wr.top; }

    function line(x1, y1, x2, y2) {
        var l = document.createElementNS(ns, 'line');
        l.setAttribute('x1', x1.toFixed(1)); l.setAttribute('y1', y1.toFixed(1));
        l.setAttribute('x2', x2.toFixed(1)); l.setAttribute('y2', y2.toFixed(1));
        l.setAttribute('stroke', '#2c3258');
        l.setAttribute('stroke-width', '2');
        l.setAttribute('stroke-linecap', 'round');
        svg.appendChild(l);
    }

    // Collect cards per round column (skip connector spacer divs)
    var byRound = [];
    var cols = wrapper.querySelectorAll('.bracket-col');
    for (var ci = 0; ci < cols.length; ci++) {
        var cards = Array.prototype.slice.call(
            cols[ci].querySelectorAll('.bracket-match-card')
        );
        if (cards.length) {
            cols[ci].style.position = 'relative';
            cols[ci].style.zIndex   = '1';
            byRound.push(cards);
        }
    }

    for (var ri = 0; ri < byRound.length - 1; ri++) {
        var froms = byRound[ri];
        var tos   = byRound[ri + 1];

        for (var ti = 0; ti < tos.length; ti++) {
            var f1 = froms[ti * 2];
            var f2 = froms[ti * 2 + 1];
            var t  = tos[ti];
            if (!f1 || !t) continue;

            var x1 = rcx(f1), y1 = mcy(f1);
            var xt = lcx(t),  yt = mcy(t);
            var xm = (x1 + xt) / 2;

            line(x1, y1, xm, y1);  // from1 → mid

            if (f2) {
                var x2 = rcx(f2), y2 = mcy(f2);
                line(x2, y2, xm, y2);              // from2 → mid
                line(xm, y1, xm, y2);              // vertical bar
                line(xm, (y1 + y2) / 2, xt, yt);  // mid → to
            } else {
                line(xm, y1, xt, yt);              // straight connector
            }
        }
    }
})();
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
