<?php
/**
 * REGISTRAR PARTIDO COMPLETO — flujo único desde cero
 * Crea PARTIDO + JUEGO + ESTADISTICAS_JUGADOR en un solo submit.
 * Soporta Bo1/Bo3/Bo5/Bo7, marcador por juego, stats por jugador.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/db.php';

requiereRol(['admin', 'editor']);

$error = '';

// ─── Datos para formulario ────────────────────────────────────
$torneos = db_fetch_all($conexion,
    "SELECT t.id_torneo, t.nombre, te.anio
     FROM TORNEO t JOIN TEMPORADA te ON te.id_temporada = t.id_temporada
     ORDER BY te.anio DESC, t.nombre");

$equipos_raw = db_fetch_all($conexion,
    "SELECT e.id_equipo, e.nombre, e.tag, r.nombre AS region
     FROM EQUIPO e JOIN REGION r ON e.id_region = r.id_region
     WHERE e.activo = 1
     ORDER BY r.nombre, e.nombre");

$jugadores_raw = db_fetch_all($conexion,
    "SELECT j.id_jugador, j.nickname, ro.id_equipo
     FROM JUGADOR j
     JOIN ROSTER ro ON ro.id_jugador = j.id_jugador AND ro.fecha_fin IS NULL
     ORDER BY ro.id_equipo, j.nickname");

// Estructura para JS: equipoId → { nombre, tag, jugadores[] }
$js_equipos = [];
foreach ($equipos_raw as $e) {
    $js_equipos[(int)$e['id_equipo']] = [
        'nombre'    => $e['nombre'],
        'tag'       => $e['tag'],
        'region'    => $e['region'],
        'jugadores' => [],
    ];
}
foreach ($jugadores_raw as $j) {
    $eid = (int)$j['id_equipo'];
    if (isset($js_equipos[$eid])) {
        $js_equipos[$eid]['jugadores'][] = [
            'id'  => (int)$j['id_jugador'],
            'nick' => $j['nickname'],
        ];
    }
}

// ─── POST handler ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_torneo  = intval($_POST['id_torneo']  ?? 0);
    $id_equipo1 = intval($_POST['id_equipo1'] ?? 0);
    $id_equipo2 = intval($_POST['id_equipo2'] ?? 0);
    $formatos_ok = ['Bo1', 'Bo3', 'Bo5', 'Bo7'];
    $formato    = in_array($_POST['formato'] ?? '', $formatos_ok) ? $_POST['formato'] : 'Bo5';
    $fecha_hora = trim($_POST['fecha_hora'] ?? '');
    $juegos_post = $_POST['juegos'] ?? [];
    $stats_post  = $_POST['stats']  ?? [];
    $mvp_row     = intval($_POST['mvp_jugador'] ?? -1);
    $id_ganador  = intval($_POST['id_ganador']  ?? 0);

    if ($id_torneo <= 0 || $id_equipo1 <= 0 || $id_equipo2 <= 0) {
        $error = "Torneo y ambos equipos son obligatorios.";
    } elseif ($id_equipo1 === $id_equipo2) {
        $error = "Los dos equipos deben ser diferentes.";
    } elseif (empty($fecha_hora)) {
        $error = "La fecha y hora son obligatorias.";
    } elseif (empty($juegos_post)) {
        $error = "Debes añadir al menos un juego.";
    } elseif ($id_ganador !== $id_equipo1 && $id_ganador !== $id_equipo2) {
        $error = "No se pudo determinar el ganador. Asegúrate de que la serie esté decidida.";
    } else {
        $fecha_dt = date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $fecha_hora)));

        $stmt = db_run($conexion,
            "INSERT INTO PARTIDO (id_torneo, id_equipo1, id_equipo2, id_ganador, fecha_hora, formato)
             VALUES (?,?,?,?,?,?)",
            "iiiiss", $id_torneo, $id_equipo1, $id_equipo2, $id_ganador, $fecha_dt, $formato
        );

        if ($stmt && $stmt->affected_rows > 0) {
            $id_partido = $stmt->insert_id;

            foreach ($juegos_post as $idx => $j) {
                $num = intval($idx) + 1;
                $g1  = intval($j['goles_eq1'] ?? 0);
                $g2  = intval($j['goles_eq2'] ?? 0);
                $dur_str = trim($j['duracion'] ?? '5:00');
                if (preg_match('/^(\d{1,3}):(\d{2})$/', $dur_str, $m)) {
                    $dur = intval($m[1]) * 60 + intval($m[2]);
                } else {
                    $dur = intval($dur_str) ?: 300;
                }
                db_run($conexion,
                    "INSERT INTO JUEGO (id_partido, numero_juego, goles_equipo1, goles_equipo2, duracion_segundos)
                     VALUES (?,?,?,?,?)",
                    "iiiii", $id_partido, $num, $g1, $g2, $dur
                );
            }

            foreach ($stats_post as $idx => $stat) {
                $id_jug = intval($stat['id_jugador'] ?? 0);
                if ($id_jug <= 0) continue;
                $sg  = intval($stat['goles']       ?? 0);
                $sa  = intval($stat['asistencias']  ?? 0);
                $sv  = intval($stat['salvadas']     ?? 0);
                $st  = intval($stat['tiros']        ?? 0);
                $mvp = ((int)$idx === $mvp_row) ? 1 : 0;
                db_run($conexion,
                    "INSERT INTO ESTADISTICAS_JUGADOR
                       (id_jugador, id_partido, goles, asistencias, salvadas, tiros, mvp)
                     VALUES (?,?,?,?,?,?,?)",
                    "iiiiiii", $id_jug, $id_partido, $sg, $sa, $sv, $st, $mvp
                );
            }

            $_SESSION['mensaje_exito'] = "Partido registrado correctamente.";
            header("Location: /RLCS/CRM/pages/partidos/index.php");
            exit();
        } else {
            $error = "Error al guardar el partido: " . mysqli_error($conexion);
        }
    }
}

$page_title = 'Registrar Partido';
require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-controller"></i> Registrar Partido
    </h2>
    <a href="/RLCS/CRM/pages/partidos/index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form id="formPartido" method="POST" novalidate>

<!-- ═══════════════════════════════════════════════════════════
     CARD 1 — Configuración
═══════════════════════════════════════════════════════════ -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-gear"></i> Configuración del Partido
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">

            <!-- Torneo -->
            <div class="col-md-5">
                <label class="form-label text-white">Torneo *</label>
                <select class="form-select bg-dark text-white border-secondary"
                        name="id_torneo" required>
                    <option value="">— Seleccionar torneo —</option>
                    <?php foreach ($torneos as $t): ?>
                    <option value="<?= $t['id_torneo'] ?>"
                            <?= (($_POST['id_torneo'] ?? '') == $t['id_torneo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?> (<?= $t['anio'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Formato -->
            <div class="col-md-2">
                <label class="form-label text-white">Formato *</label>
                <select class="form-select bg-dark text-white border-secondary"
                        name="formato" id="selectFormato" onchange="onFormatoChange()">
                    <?php foreach (['Bo3','Bo5','Bo7','Bo1'] as $fmt): ?>
                    <option value="<?= $fmt ?>"
                            <?= (($_POST['formato'] ?? 'Bo5') === $fmt) ? 'selected' : '' ?>>
                        <?= $fmt ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Fecha -->
            <div class="col-md-3">
                <label class="form-label text-white">Fecha y Hora *</label>
                <input type="datetime-local"
                       class="form-control bg-dark text-white border-secondary"
                       name="fecha_hora" id="fecha_hora" required
                       value="<?= htmlspecialchars($_POST['fecha_hora'] ?? '') ?>">
            </div>

            <!-- Equipo 1 -->
            <div class="col-md-5">
                <label class="form-label" style="color:var(--color-accent)">
                    <i class="bi bi-shield-fill"></i> Equipo 1 *
                </label>
                <select class="form-select bg-dark text-white border-secondary"
                        name="id_equipo1" id="id_equipo1"
                        required onchange="onTeamsChange()">
                    <option value="">— Seleccionar —</option>
                    <?php
                    $rg = '';
                    foreach ($equipos_raw as $e) {
                        if ($e['region'] !== $rg) {
                            if ($rg) echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($e['region']) . '">';
                            $rg = $e['region'];
                        }
                    ?>
                    <option value="<?= $e['id_equipo'] ?>"
                            <?= (($_POST['id_equipo1'] ?? '') == $e['id_equipo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre']) ?> [<?= htmlspecialchars($e['tag']) ?>]
                    </option>
                    <?php } if ($rg) echo '</optgroup>'; ?>
                </select>
            </div>

            <!-- VS badge -->
            <div class="col-md-2 d-flex align-items-end justify-content-center pb-2">
                <span class="badge bg-secondary fs-6">VS</span>
            </div>

            <!-- Equipo 2 -->
            <div class="col-md-5">
                <label class="form-label text-warning">
                    <i class="bi bi-shield-fill"></i> Equipo 2 *
                </label>
                <select class="form-select bg-dark text-white border-secondary"
                        name="id_equipo2" id="id_equipo2"
                        required onchange="onTeamsChange()">
                    <option value="">— Seleccionar —</option>
                    <?php
                    $rg = '';
                    foreach ($equipos_raw as $e) {
                        if ($e['region'] !== $rg) {
                            if ($rg) echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($e['region']) . '">';
                            $rg = $e['region'];
                        }
                    ?>
                    <option value="<?= $e['id_equipo'] ?>"
                            <?= (($_POST['id_equipo2'] ?? '') == $e['id_equipo']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre']) ?> [<?= htmlspecialchars($e['tag']) ?>]
                    </option>
                    <?php } if ($rg) echo '</optgroup>'; ?>
                </select>
            </div>

        </div><!-- /row -->
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     CARD 2 — Juegos
═══════════════════════════════════════════════════════════ -->
<div class="card bg-dark border-secondary mb-4" id="secJuegos" style="display:none">
    <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-joystick"></i> Juegos
        </h5>
        <span id="formatoBadge" class="badge bg-secondary">Bo5</span>
    </div>
    <div class="card-body">

        <!-- Marcador -->
        <div class="rounded p-3 mb-4 d-flex align-items-center justify-content-center gap-5"
             style="background:#0a0a1a;border:1px solid #2a2a4a">
            <div class="text-center" style="min-width:130px">
                <div class="small mb-1" id="sb_tag1" style="color:var(--color-accent)">—</div>
                <div class="display-3 fw-bold" id="sb_wins1" style="color:var(--color-accent)">0</div>
                <div class="small text-muted" id="sb_need1">necesita 3 victorias</div>
            </div>
            <div class="text-center">
                <div class="text-muted small mb-1" id="sb_fmt">Bo5</div>
                <div class="text-white fw-bold fs-5">–</div>
                <div class="text-muted small mt-1" id="sb_games">0 jugados</div>
            </div>
            <div class="text-center" style="min-width:130px">
                <div class="small mb-1 text-warning" id="sb_tag2">—</div>
                <div class="display-3 fw-bold text-warning" id="sb_wins2">0</div>
                <div class="small text-muted" id="sb_need2">necesita 3 victorias</div>
            </div>
        </div>

        <!-- Banner ganador -->
        <div id="winnerBanner" class="alert alert-success d-flex align-items-center gap-2 mb-3"
             style="display:none!important">
            <i class="bi bi-trophy-fill fs-5"></i>
            <div>
                <strong id="winnerName">—</strong> gana la serie
                <span class="text-muted ms-1" id="finalScore"></span>
            </div>
        </div>

        <!-- Tabla de juegos -->
        <div id="gamesTableWrap" style="display:none" class="mb-4">
            <table class="table table-dark table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th class="text-center text-muted" style="width:60px">Juego</th>
                        <th class="text-center" id="th_eq1" style="color:var(--color-accent)">EQ1</th>
                        <th class="text-center text-muted" style="width:30px">–</th>
                        <th class="text-center text-warning" id="th_eq2">EQ2</th>
                        <th class="text-center text-muted" style="width:100px">Duración</th>
                        <th class="text-center text-muted" style="width:90px">Ganador</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="gamesTbody"></tbody>
            </table>
        </div>

        <!-- Formulario añadir juego -->
        <div class="p-3 rounded" style="background:#0d0d20;border:1px solid #2a2a4a">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label text-white small mb-1" id="lbl_g1">EQ1 Goles</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary text-center"
                           id="addG1" min="0" value="0" style="width:70px"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();addGame()}">
                </div>
                <div class="col-auto d-flex align-items-end" style="padding-bottom:8px">
                    <span class="text-muted fw-bold fs-5">–</span>
                </div>
                <div class="col-auto">
                    <label class="form-label text-warning small mb-1" id="lbl_g2">EQ2 Goles</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary text-center"
                           id="addG2" min="0" value="0" style="width:70px"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();addGame()}">
                </div>
                <div class="col-auto">
                    <label class="form-label text-white small mb-1">
                        Duración
                        <span class="text-muted" style="font-size:.75em">(mm:ss)</span>
                    </label>
                    <input type="text" class="form-control bg-dark text-white border-secondary text-center"
                           id="addDur" value="5:00" placeholder="5:00" style="width:85px"
                           pattern="\d{1,3}:\d{2}"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();addGame()}">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1 d-block" style="color:transparent">·</label>
                    <button type="button" class="btn btn-accent" id="btnAddGame" onclick="addGame()">
                        <i class="bi bi-plus-circle"></i> Añadir Juego
                    </button>
                </div>
                <div class="col d-flex align-items-end">
                    <div id="addHint" class="text-muted small pb-1"></div>
                </div>
            </div>
        </div>

        <!-- Inputs ocultos generados por JS -->
        <div id="hiddenJuegos"></div>

    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     CARD 3 — Estadísticas (opcional)
═══════════════════════════════════════════════════════════ -->
<div class="card bg-dark border-secondary mb-4" id="secStats" style="display:none">
    <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-warning">
            <i class="bi bi-bar-chart-line"></i> Estadísticas del Partido
            <span class="badge bg-secondary ms-2 fw-normal" style="font-size:.7em">Opcional</span>
        </h5>
        <button type="button" class="btn btn-sm btn-outline-secondary"
                onclick="toggleStats()" id="btnToggleStats">
            <i class="bi bi-chevron-down"></i> Mostrar
        </button>
    </div>
    <div id="statsBody" style="display:none">
        <div class="card-body border-bottom border-secondary pb-2">
            <p class="text-muted small mb-0">
                <i class="bi bi-info-circle"></i>
                Las estadísticas son el <strong>agregado total del partido</strong> (suma de todos los juegos).
                El jugador marcado como <strong class="text-warning">MVP</strong> es el MVP del partido completo.
                Deja el selector en blanco para omitir una fila.
            </p>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" id="statsTable">
                <thead>
                    <tr class="small text-muted">
                        <th style="width:90px">Equipo</th>
                        <th>Jugador</th>
                        <th class="text-center" style="width:70px">Goles</th>
                        <th class="text-center" style="width:80px">Asist.</th>
                        <th class="text-center" style="width:80px">Salvadas</th>
                        <th class="text-center" style="width:70px">Tiros</th>
                        <th class="text-center" style="width:60px">
                            <i class="bi bi-star-fill text-warning"></i> MVP
                        </th>
                    </tr>
                </thead>
                <tbody id="statsTbody">
                    <!-- generado por JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Inputs ocultos de control -->
<input type="hidden" name="id_ganador" id="id_ganador">

<!-- ═══════════════════════════════════════════════════════════
     Botones finales
═══════════════════════════════════════════════════════════ -->
<div id="submitArea" style="display:none" class="mb-5">
    <div class="d-flex gap-3 align-items-center flex-wrap">
        <button type="submit" class="btn btn-success btn-lg px-5" id="btnSubmit">
            <i class="bi bi-check-circle-fill"></i> Registrar Partido
        </button>
        <a href="/RLCS/CRM/pages/partidos/index.php" class="btn btn-outline-secondary btn-lg">
            Cancelar
        </a>
        <div id="submitHint" class="text-muted small ms-2"></div>
    </div>
</div>

</form><!-- /formPartido -->

<!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════════ -->
<script>
// ── Datos del servidor ───────────────────────────────────────
const EQUIPOS = <?= json_encode(array_values($js_equipos) !== $js_equipos
    ? $js_equipos
    : array_combine(array_keys($js_equipos), $js_equipos),
    JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?>;

// ── Estado ───────────────────────────────────────────────────
let games   = [];   // [{g1, g2, dur}]
let formato = document.getElementById('selectFormato').value;

const MAX_WINS = { Bo1: 1, Bo3: 2, Bo5: 3, Bo7: 4 };
const maxWins  = () => MAX_WINS[formato] || 3;
const maxGames = () => maxWins() * 2 - 1;

// ── Helpers ──────────────────────────────────────────────────
function getEq1Id() { return parseInt(document.getElementById('id_equipo1').value) || 0; }
function getEq2Id() { return parseInt(document.getElementById('id_equipo2').value) || 0; }
function getEq(id)  { return EQUIPOS[id] || null; }

function getWins() {
    let w1 = 0, w2 = 0;
    games.forEach(g => {
        if      (g.g1 > g.g2) w1++;
        else if (g.g2 > g.g1) w2++;
    });
    return [w1, w2];
}

function seriesDecided() {
    const [w1, w2] = getWins();
    return w1 >= maxWins() || w2 >= maxWins();
}

function escH(str) {
    const d = document.createElement('div');
    d.textContent = String(str);
    return d.innerHTML;
}

// ── Evento: cambio de equipos / formato ─────────────────────
function onTeamsChange() {
    const e1 = getEq1Id(), e2 = getEq2Id();
    if (!e1 || !e2 || e1 === e2) return;

    document.getElementById('secJuegos').style.display = '';
    updateLabels();
    updateScoreboard();
    rebuildStatsTable();
    updateSubmitArea();
}

function onFormatoChange() {
    formato = document.getElementById('selectFormato').value;
    document.getElementById('formatoBadge').textContent = formato;
    document.getElementById('sb_fmt').textContent = formato;
    updateScoreboard();
    updateSubmitArea();
}

function updateLabels() {
    const e1 = getEq(getEq1Id()), e2 = getEq(getEq2Id());
    if (!e1 || !e2) return;

    document.getElementById('sb_tag1').textContent = e1.tag;
    document.getElementById('sb_tag2').textContent = e2.tag;
    document.getElementById('th_eq1').textContent  = e1.tag;
    document.getElementById('th_eq2').textContent  = e2.tag;
    document.getElementById('lbl_g1').textContent  = e1.tag + ' Goles';
    document.getElementById('lbl_g2').textContent  = e2.tag + ' Goles';
    document.getElementById('formatoBadge').textContent = formato;
    document.getElementById('sb_fmt').textContent = formato;
}

// ── Marcador ─────────────────────────────────────────────────
function updateScoreboard() {
    const [w1, w2] = getWins();
    const mw = maxWins();
    const e1 = getEq(getEq1Id()), e2 = getEq(getEq2Id());

    document.getElementById('sb_wins1').textContent = w1;
    document.getElementById('sb_wins2').textContent = w2;
    document.getElementById('sb_games').textContent = games.length + ' jugado' + (games.length !== 1 ? 's' : '');
    document.getElementById('sb_need1').textContent = w1 >= mw ? '¡GANADOR!' : 'faltan ' + (mw - w1);
    document.getElementById('sb_need2').textContent = w2 >= mw ? '¡GANADOR!' : 'faltan ' + (mw - w2);

    const banner = document.getElementById('winnerBanner');
    if (seriesDecided() && e1 && e2) {
        const winner = w1 >= mw ? e1 : e2;
        document.getElementById('winnerName').textContent  = winner.nombre;
        document.getElementById('finalScore').textContent  = '(' + w1 + '–' + w2 + ')';
        banner.style.display = '';
        document.getElementById('id_ganador').value = w1 >= mw ? getEq1Id() : getEq2Id();
    } else {
        banner.style.display = 'none';
        document.getElementById('id_ganador').value = '';
    }

    // Estado botón añadir
    const decided = seriesDecided();
    const btn = document.getElementById('btnAddGame');
    btn.disabled = decided;

    const remaining = maxGames() - games.length;
    if (decided) {
        document.getElementById('addHint').textContent = 'Serie finalizada (' + games.length + ' juegos)';
    } else if (remaining > 0) {
        document.getElementById('addHint').textContent =
            'Juego ' + (games.length + 1) + ' · máximo ' + maxGames() + ' (' + formato + ')';
    }
}

// ── Añadir juego ─────────────────────────────────────────────
function addGame() {
    if (seriesDecided()) return;

    const g1  = parseInt(document.getElementById('addG1').value) || 0;
    const g2  = parseInt(document.getElementById('addG2').value) || 0;
    const dur = document.getElementById('addDur').value.trim() || '5:00';

    if (g1 === g2) {
        alert('En Rocket League no puede haber empate (hay prórroga). Por favor, indica el marcador final incluyendo el gol de prórroga.');
        document.getElementById('addG1').focus();
        return;
    }

    // Validar que el juego extra tiene sentido (un equipo no puede superar max_wins antes del límite)
    const [w1, w2] = getWins();
    if (g1 > g2 && w1 >= maxWins()) { alert('El equipo 1 ya ganó la serie.'); return; }
    if (g2 > g1 && w2 >= maxWins()) { alert('El equipo 2 ya ganó la serie.'); return; }

    // Validar formato duración
    if (!/^\d{1,3}:\d{2}$/.test(dur)) {
        alert('Formato de duración incorrecto. Usa MM:SS (ej: 5:00, 5:34, 10:00)');
        document.getElementById('addDur').focus();
        return;
    }

    games.push({ g1, g2, dur });

    document.getElementById('addG1').value = 0;
    document.getElementById('addG2').value = 0;
    document.getElementById('addDur').value = '5:00';
    document.getElementById('addG1').focus();

    renderGamesTable();
    buildHiddenJuegos();
    updateScoreboard();
    updateSubmitArea();
}

function removeGame(idx) {
    games.splice(idx, 1);
    renderGamesTable();
    buildHiddenJuegos();
    updateScoreboard();
    updateSubmitArea();
}

// ── Tabla de juegos ──────────────────────────────────────────
function renderGamesTable() {
    const wrap  = document.getElementById('gamesTableWrap');
    const tbody = document.getElementById('gamesTbody');
    const e1    = getEq(getEq1Id());
    const e2    = getEq(getEq2Id());

    if (games.length === 0) { wrap.style.display = 'none'; tbody.innerHTML = ''; return; }
    wrap.style.display = '';

    let html = '';
    games.forEach((g, i) => {
        const w1 = g.g1 > g.g2, w2 = g.g2 > g.g1;
        const winTag = w1 ? (e1 ? escH(e1.tag) : 'EQ1') : (e2 ? escH(e2.tag) : 'EQ2');
        const winColor = w1 ? 'var(--color-accent)' : '#ffc107';

        html += '<tr>';
        html += '<td class="text-center text-muted small align-middle">G' + (i + 1) + '</td>';
        html += '<td class="text-center fw-bold align-middle fs-5" style="color:' + (w1 ? 'var(--color-accent)' : '#6c757d') + '">' + g.g1 + '</td>';
        html += '<td class="text-center text-muted align-middle">–</td>';
        html += '<td class="text-center fw-bold align-middle fs-5" style="color:' + (w2 ? '#ffc107' : '#6c757d') + '">' + g.g2 + '</td>';
        html += '<td class="text-center text-muted small align-middle">' + escH(g.dur) + '</td>';
        html += '<td class="text-center align-middle"><span class="badge" style="background:' + winColor + ';color:#000">' + winTag + '</span></td>';
        html += '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm py-0 px-1" onclick="removeGame(' + i + ')" title="Eliminar juego"><i class="bi bi-x"></i></button></td>';
        html += '</tr>';
    });
    tbody.innerHTML = html;
}

// ── Inputs ocultos de juegos ──────────────────────────────────
function buildHiddenJuegos() {
    let html = '';
    games.forEach((g, i) => {
        html += '<input type="hidden" name="juegos[' + i + '][goles_eq1]" value="' + g.g1 + '">';
        html += '<input type="hidden" name="juegos[' + i + '][goles_eq2]" value="' + g.g2 + '">';
        html += '<input type="hidden" name="juegos[' + i + '][duracion]"  value="' + escH(g.dur) + '">';
    });
    document.getElementById('hiddenJuegos').innerHTML = html;
}

// ── Botón submit ─────────────────────────────────────────────
function updateSubmitArea() {
    const area = document.getElementById('submitArea');
    const hint = document.getElementById('submitHint');

    if (games.length === 0) {
        area.style.display = 'none';
        return;
    }
    area.style.display = '';

    if (!seriesDecided()) {
        hint.innerHTML = '<i class="bi bi-exclamation-triangle text-warning"></i> La serie aún no tiene ganador — puedes guardar igualmente.';
    } else {
        hint.textContent = '';
    }
}

// ── Estadísticas (toggle + tabla) ───────────────────────────
function toggleStats() {
    const body = document.getElementById('statsBody');
    const btn  = document.getElementById('btnToggleStats');
    if (body.style.display === 'none') {
        body.style.display = '';
        btn.innerHTML = '<i class="bi bi-chevron-up"></i> Ocultar';
        rebuildStatsTable();
    } else {
        body.style.display = 'none';
        btn.innerHTML = '<i class="bi bi-chevron-down"></i> Mostrar';
    }
}

function rebuildStatsTable() {
    const e1Id = getEq1Id(), e2Id = getEq2Id();
    const e1 = getEq(e1Id), e2 = getEq(e2Id);
    if (!e1 || !e2) return;

    document.getElementById('secStats').style.display = '';

    const tbody = document.getElementById('statsTbody');
    let html = '';

    const slots = [
        { eqData: e1, startIdx: 0, color: 'var(--color-accent)', colorDark: '#001a20' },
        { eqData: e2, startIdx: 3, color: '#ffc107',             colorDark: '#1a1500' },
    ];

    slots.forEach(slot => {
        const jugs = (slot.eqData.jugadores || []).slice(0, 6);
        for (let i = 0; i < 3; i++) {
            const gi  = slot.startIdx + i;
            const jug = jugs[i] || null;
            const rowStyle = i % 2 === 0 ? '' : 'background:rgba(255,255,255,.02)';

            html += '<tr style="' + rowStyle + '">';
            // Badge equipo (solo primera fila del grupo → rowspan)
            if (i === 0) {
                html += '<td rowspan="3" class="align-middle text-center">';
                html += '<span class="badge d-block" style="background:' + slot.color + ';color:#000;font-size:.85em;padding:.4em .6em">';
                html += escH(slot.eqData.tag) + '</span></td>';
            }

            // Selector jugador
            html += '<td class="align-middle">';
            html += '<select class="form-select form-select-sm bg-dark text-white border-secondary" ';
            html += 'name="stats[' + gi + '][id_jugador]" style="min-width:170px">';
            html += '<option value="">— Sin jugador —</option>';
            jugs.forEach(j => {
                const sel = (jug && j.id === jug.id) ? ' selected' : '';
                html += '<option value="' + j.id + '"' + sel + '>' + escH(j.nick) + '</option>';
            });
            html += '</select></td>';

            // Inputs numéricos
            ['goles','asistencias','salvadas','tiros'].forEach(campo => {
                html += '<td class="align-middle"><input type="number" class="form-control form-control-sm bg-dark text-white border-secondary text-center" ';
                html += 'name="stats[' + gi + '][' + campo + ']" min="0" value="0" style="width:60px"></td>';
            });

            // MVP radio
            html += '<td class="text-center align-middle">';
            html += '<input type="radio" class="form-check-input" name="mvp_jugador" ';
            html += 'value="' + gi + '" style="width:1.2em;height:1.2em;cursor:pointer" ';
            html += (gi === 0 ? 'checked' : '') + '>';
            html += '</td>';

            html += '</tr>';
        }
    });

    tbody.innerHTML = html;
}

// ── Validación submit ────────────────────────────────────────
document.getElementById('formPartido').addEventListener('submit', function(e) {
    const torneo = document.querySelector('[name="id_torneo"]').value;
    const e1     = getEq1Id(), e2 = getEq2Id();
    const fecha  = document.getElementById('fecha_hora').value;
    const gana   = document.getElementById('id_ganador').value;

    if (!torneo)          { e.preventDefault(); alert('Selecciona un torneo.'); return; }
    if (!e1 || !e2)       { e.preventDefault(); alert('Selecciona ambos equipos.'); return; }
    if (e1 === e2)        { e.preventDefault(); alert('Los equipos deben ser diferentes.'); return; }
    if (!fecha)           { e.preventDefault(); alert('Introduce la fecha y hora del partido.'); return; }
    if (games.length < 1) { e.preventDefault(); alert('Añade al menos un juego.'); return; }
    if (!gana)            {
        // Calcular ganador manual si no está decidida la serie
        const [w1, w2] = getWins();
        if (w1 > w2) {
            document.getElementById('id_ganador').value = e1;
        } else if (w2 > w1) {
            document.getElementById('id_ganador').value = e2;
        } else {
            e.preventDefault();
            alert('No se puede determinar el ganador (marcador empatado). Añade más juegos.');
            return;
        }
    }
});

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Fecha y hora actual por defecto
    if (!document.getElementById('fecha_hora').value) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('fecha_hora').value = now.toISOString().slice(0,16);
    }

    // Si hay equipos pre-seleccionados (vuelta tras error POST)
    if (getEq1Id() && getEq2Id()) {
        onTeamsChange();
    }

    updateScoreboard();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
