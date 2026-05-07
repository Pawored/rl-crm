<?php
/**
 * PARTIDOS - Registrar resultado
 * Seleccionar partido sin ganador, elegir ganador y registrar.
 * Llama a CALL registrar_resultado_partido().
 * También permite añadir juegos individuales (goles, duración).
 * Solo admin y editor.
 */

require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/db.php';

// --- Solo admin y editor ---
requiereRol(['admin', 'editor']);

// === LÓGICA PHP ===

$error = '';

// Partido seleccionado para la sección de juegos (GET param)
$id_partido_activo = intval($_GET['partido'] ?? $_GET['id'] ?? 0);

// --- Obtener partidos sin ganador (pendientes) ---
$res_pendientes = mysqli_query($conexion,
    "SELECT p.id_partido, p.fecha_hora, p.formato,
            e1.nombre AS equipo1, e1.tag AS tag1, e1.id_equipo AS id_eq1,
            e2.nombre AS equipo2, e2.tag AS tag2, e2.id_equipo AS id_eq2
     FROM PARTIDO p
     INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
     INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
     WHERE p.id_ganador IS NULL
     ORDER BY p.fecha_hora DESC");

// --- Procesar registro de resultado ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'resultado') {
    $id_partido = intval($_POST['id_partido'] ?? 0);
    $id_ganador = intval($_POST['id_ganador'] ?? 0);

    if ($id_partido <= 0 || $id_ganador <= 0) {
        $error = "Debes seleccionar un partido y un ganador.";
    } else {
        if (mysqli_query($conexion, "CALL registrar_resultado_partido($id_partido, $id_ganador)")) {
            $_SESSION['mensaje_exito'] = "Resultado registrado correctamente.";
            header("Location: /RLCS/CRM/pages/partidos/index.php");
            exit();
        } else {
            $error = "Error al registrar resultado: " . mysqli_error($conexion);
        }
    }
}

// --- Procesar añadir juego (prepared statement + PRG) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'juego') {
    $id_partido_juego = intval($_POST['id_partido_juego'] ?? 0);
    $numero_juego     = intval($_POST['numero_juego']     ?? 1);
    $goles_eq1        = intval($_POST['goles_equipo1']    ?? 0);
    $goles_eq2        = intval($_POST['goles_equipo2']    ?? 0);
    $duracion         = intval($_POST['duracion']         ?? 300);

    if ($id_partido_juego <= 0) {
        $error = "Debes seleccionar un partido para añadir el juego.";
    } else {
        $stmt = db_run($conexion,
            "INSERT INTO JUEGO (id_partido, numero_juego, goles_equipo1, goles_equipo2, duracion_segundos)
             VALUES (?, ?, ?, ?, ?)",
            "iiiii", $id_partido_juego, $numero_juego, $goles_eq1, $goles_eq2, $duracion
        );
        if ($stmt && $stmt->affected_rows > 0) {
            $_SESSION['mensaje_exito'] = "Juego #$numero_juego añadido: {$goles_eq1}–{$goles_eq2}.";
            header("Location: /RLCS/CRM/pages/partidos/registrar.php?partido=$id_partido_juego");
            exit();
        } else {
            $error = "Error al añadir juego: " . mysqli_error($conexion);
            $id_partido_activo = $id_partido_juego;
        }
    }
}

// --- Todos los partidos para el selector de juegos ---
$res_todos = mysqli_query($conexion,
    "SELECT p.id_partido, p.fecha_hora,
            e1.tag AS tag1, e2.tag AS tag2
     FROM PARTIDO p
     INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
     INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
     ORDER BY p.fecha_hora DESC");

// --- Juegos existentes del partido activo ---
$partido_activo    = null;
$juegos_existentes = [];
$siguiente_juego   = 1;
if ($id_partido_activo > 0) {
    $partido_activo = db_fetch_one($conexion,
        "SELECT p.id_partido, p.formato,
                e1.nombre AS equipo1, e1.tag AS tag1,
                e2.nombre AS equipo2, e2.tag AS tag2
         FROM PARTIDO p
         INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
         INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
         WHERE p.id_partido = ?",
        "i", $id_partido_activo
    );
    if ($partido_activo) {
        $juegos_existentes = db_fetch_all($conexion,
            "SELECT * FROM JUEGO WHERE id_partido = ? ORDER BY numero_juego ASC",
            "i", $id_partido_activo
        );
        $siguiente_juego = count($juegos_existentes) + 1;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-check-circle"></i> Registrar Resultado
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

<!-- ========== SECCIÓN 1: REGISTRAR GANADOR ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-trophy"></i> Registrar Ganador de un Partido
        </h5>
    </div>
    <div class="card-body">
        <?php if ($res_pendientes && mysqli_num_rows($res_pendientes) > 0): ?>
            <form method="POST" id="formResultado">
                <input type="hidden" name="accion" value="resultado">
                <div class="row">
                    <!-- Seleccionar partido -->
                    <div class="col-md-6 mb-3">
                        <label for="id_partido" class="form-label text-white">Seleccionar Partido *</label>
                        <select class="form-select bg-dark text-white border-secondary"
                                id="id_partido" name="id_partido" required
                                onchange="actualizarOpciones()">
                            <option value="">-- Seleccionar partido --</option>
                            <?php
                            mysqli_data_seek($res_pendientes, 0);
                            while ($p = mysqli_fetch_assoc($res_pendientes)):
                            ?>
                                <option value="<?= $p['id_partido'] ?>"
                                        data-eq1-id="<?= $p['id_eq1'] ?>"
                                        data-eq1-nombre="<?= htmlspecialchars($p['tag1'] . ' - ' . $p['equipo1']) ?>"
                                        data-eq2-id="<?= $p['id_eq2'] ?>"
                                        data-eq2-nombre="<?= htmlspecialchars($p['tag2'] . ' - ' . $p['equipo2']) ?>"
                                        <?= ($id_preseleccionado == $p['id_partido']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['tag1']) ?> vs <?= htmlspecialchars($p['tag2']) ?>
                                    <?= $p['fecha_hora'] ? ' (' . date('d/m/Y', strtotime($p['fecha_hora'])) . ')' : '' ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- Seleccionar ganador -->
                    <div class="col-md-4 mb-3">
                        <label for="id_ganador" class="form-label text-white">Seleccionar Ganador *</label>
                        <select class="form-select bg-dark text-white border-secondary"
                                id="id_ganador" name="id_ganador" required>
                            <option value="">-- Primero selecciona un partido --</option>
                        </select>
                    </div>
                    <!-- Botón -->
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Registrar
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle"></i> No hay partidos pendientes de resultado.
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- ========== SECCIÓN 2: JUEGOS ========== -->
<div class="card bg-dark border-secondary mb-4" id="juegos">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-controller"></i> Registrar Juegos de un Partido
        </h5>
    </div>
    <div class="card-body">

        <!-- Selector de partido activo -->
        <form method="GET" class="d-flex gap-2 align-items-end mb-4">
            <div class="flex-grow-1" style="max-width:360px">
                <label class="form-label text-white small">Seleccionar partido</label>
                <select name="partido" class="form-select bg-dark text-white border-secondary"
                        onchange="this.form.submit()">
                    <option value="">-- Seleccionar partido --</option>
                    <?php
                    mysqli_data_seek($res_todos, 0);
                    while ($pt = mysqli_fetch_assoc($res_todos)):
                    ?>
                        <option value="<?= $pt['id_partido'] ?>"
                                <?= $id_partido_activo == $pt['id_partido'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pt['tag1']) ?> vs <?= htmlspecialchars($pt['tag2']) ?>
                            <?= $pt['fecha_hora'] ? ' (' . date('d/m/Y', strtotime($pt['fecha_hora'])) . ')' : '' ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <?php if ($partido_activo): ?>

        <!-- Marcador acumulado y juegos existentes -->
        <?php
        $wins1 = $wins2 = 0;
        foreach ($juegos_existentes as $j) {
            if ($j['goles_equipo1'] > $j['goles_equipo2']) $wins1++;
            elseif ($j['goles_equipo2'] > $j['goles_equipo1']) $wins2++;
        }
        ?>
        <div class="d-flex align-items-center justify-content-center gap-4 mb-4 py-3
                    border border-secondary rounded" style="background:#12122a">
            <div class="text-center">
                <div class="fw-bold text-white"><?= htmlspecialchars($partido_activo['tag1']) ?></div>
                <div class="display-5 fw-bold text-accent"><?= $wins1 ?></div>
            </div>
            <div class="text-muted">
                <span class="badge bg-secondary"><?= htmlspecialchars($partido_activo['formato']) ?></span>
            </div>
            <div class="text-center">
                <div class="fw-bold text-white"><?= htmlspecialchars($partido_activo['tag2']) ?></div>
                <div class="display-5 fw-bold text-accent"><?= $wins2 ?></div>
            </div>
        </div>

        <?php if (!empty($juegos_existentes)): ?>
        <table class="table table-dark table-sm mb-4">
            <thead>
                <tr>
                    <th class="text-center">Juego</th>
                    <th class="text-center"><?= htmlspecialchars($partido_activo['equipo1']) ?></th>
                    <th class="text-center">–</th>
                    <th class="text-center"><?= htmlspecialchars($partido_activo['equipo2']) ?></th>
                    <th class="text-center">Duración</th>
                    <th class="text-center">Ganador</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($juegos_existentes as $j): ?>
            <tr>
                <td class="text-center text-muted">G<?= $j['numero_juego'] ?></td>
                <td class="text-center <?= $j['goles_equipo1'] > $j['goles_equipo2'] ? 'text-success fw-bold' : '' ?>">
                    <?= $j['goles_equipo1'] ?>
                </td>
                <td class="text-center text-muted">–</td>
                <td class="text-center <?= $j['goles_equipo2'] > $j['goles_equipo1'] ? 'text-success fw-bold' : '' ?>">
                    <?= $j['goles_equipo2'] ?>
                </td>
                <td class="text-center text-muted small">
                    <?php
                    $s = intval($j['duracion_segundos']);
                    printf('%d:%02d', intdiv($s, 60), $s % 60);
                    ?>
                </td>
                <td class="text-center">
                    <?php if ($j['goles_equipo1'] > $j['goles_equipo2']): ?>
                        <span class="badge bg-success"><?= htmlspecialchars($partido_activo['tag1']) ?></span>
                    <?php elseif ($j['goles_equipo2'] > $j['goles_equipo1']): ?>
                        <span class="badge bg-success"><?= htmlspecialchars($partido_activo['tag2']) ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Empate OT</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Formulario añadir juego -->
        <form method="POST">
            <input type="hidden" name="accion" value="juego">
            <input type="hidden" name="id_partido_juego" value="<?= $id_partido_activo ?>">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label text-white small">Nº Juego</label>
                    <input type="number" class="form-control form-control-sm bg-dark text-white border-secondary"
                           name="numero_juego" min="1" value="<?= $siguiente_juego ?>"
                           style="width:80px" required>
                </div>
                <div class="col-auto">
                    <label class="form-label text-white small">
                        <?= htmlspecialchars($partido_activo['tag1']) ?> goles
                    </label>
                    <input type="number" class="form-control form-control-sm bg-dark text-white border-secondary"
                           name="goles_equipo1" min="0" value="0" style="width:80px" required>
                </div>
                <div class="col-auto d-flex align-items-end pb-1">
                    <span class="text-muted fw-bold">–</span>
                </div>
                <div class="col-auto">
                    <label class="form-label text-white small">
                        <?= htmlspecialchars($partido_activo['tag2']) ?> goles
                    </label>
                    <input type="number" class="form-control form-control-sm bg-dark text-white border-secondary"
                           name="goles_equipo2" min="0" value="0" style="width:80px" required>
                </div>
                <div class="col-auto">
                    <label class="form-label text-white small">Duración (seg)</label>
                    <input type="number" class="form-control form-control-sm bg-dark text-white border-secondary"
                           name="duracion" min="0" value="300" style="width:100px" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-accent btn-sm">
                        <i class="bi bi-plus-circle"></i> Añadir juego
                    </button>
                </div>
            </div>
        </form>

        <?php else: ?>
        <p class="text-muted mb-0">
            <i class="bi bi-info-circle"></i>
            Selecciona un partido arriba para ver sus juegos y añadir nuevos.
        </p>
        <?php endif; ?>
    </div>
</div>

<!-- ========== SCRIPT: Actualizar opciones de ganador ========== -->
<script>
function actualizarOpciones() {
    const selectPartido = document.getElementById('id_partido');
    const selectGanador = document.getElementById('id_ganador');
    const opcion = selectPartido.options[selectPartido.selectedIndex];

    // Limpiar opciones del ganador
    selectGanador.innerHTML = '<option value="">-- Seleccionar ganador --</option>';

    if (opcion.value) {
        // Añadir equipo 1 como opción
        const opt1 = document.createElement('option');
        opt1.value = opcion.dataset.eq1Id;
        opt1.textContent = opcion.dataset.eq1Nombre;
        selectGanador.appendChild(opt1);

        // Añadir equipo 2 como opción
        const opt2 = document.createElement('option');
        opt2.value = opcion.dataset.eq2Id;
        opt2.textContent = opcion.dataset.eq2Nombre;
        selectGanador.appendChild(opt2);
    }
}

// Si hay partido preseleccionado, ejecutar al cargar
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('id_partido').value) {
        actualizarOpciones();
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
