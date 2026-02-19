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

// --- Solo admin y editor ---
requiereRol(['admin', 'editor']);

// === LÓGICA PHP ===

$error = '';
$exito_juego = '';

// --- Obtener partidos sin ganador (pendientes) ---
$sql_pendientes = "SELECT p.id_partido, p.fecha_hora, p.formato,
                          e1.nombre AS equipo1, e1.tag AS tag1, e1.id_equipo AS id_eq1,
                          e2.nombre AS equipo2, e2.tag AS tag2, e2.id_equipo AS id_eq2
                   FROM PARTIDO p
                   INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
                   INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
                   WHERE p.id_ganador IS NULL
                   ORDER BY p.fecha_hora DESC";
$res_pendientes = mysqli_query($conexion, $sql_pendientes);

// --- Si viene un ?id=X, preseleccionar ese partido ---
$id_preseleccionado = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- Procesar registro de resultado ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'resultado') {
    $id_partido = intval($_POST['id_partido'] ?? 0);
    $id_ganador = intval($_POST['id_ganador'] ?? 0);

    if ($id_partido <= 0 || $id_ganador <= 0) {
        $error = "Debes seleccionar un partido y un ganador.";
    } else {
        // Llamar al procedimiento almacenado
        $sql_call = "CALL registrar_resultado_partido($id_partido, $id_ganador)";
        if (mysqli_query($conexion, $sql_call)) {
            $_SESSION['mensaje_exito'] = "Resultado registrado correctamente.";
            header("Location: /RLCS/CRM/pages/partidos/index.php");
            exit();
        } else {
            $error = "Error al registrar resultado: " . mysqli_error($conexion);
        }
    }
}

// --- Procesar añadir juego ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'juego') {
    $id_partido_juego = intval($_POST['id_partido_juego'] ?? 0);
    $numero_juego     = intval($_POST['numero_juego'] ?? 1);
    $goles_eq1        = intval($_POST['goles_equipo1'] ?? 0);
    $goles_eq2        = intval($_POST['goles_equipo2'] ?? 0);
    $duracion         = intval($_POST['duracion'] ?? 300);

    if ($id_partido_juego <= 0) {
        $error = "Debes seleccionar un partido para añadir el juego.";
    } else {
        $sql_juego = "INSERT INTO JUEGO (id_partido, numero_juego, goles_equipo1, goles_equipo2, duracion_segundos)
                      VALUES ($id_partido_juego, $numero_juego, $goles_eq1, $goles_eq2, $duracion)";
        if (mysqli_query($conexion, $sql_juego)) {
            $exito_juego = "Juego #$numero_juego añadido correctamente.";
        } else {
            $error = "Error al añadir juego: " . mysqli_error($conexion);
        }
    }
}

// --- Obtener todos los partidos para la sección de juegos ---
$sql_todos = "SELECT p.id_partido, p.fecha_hora,
                     e1.tag AS tag1, e2.tag AS tag2
              FROM PARTIDO p
              INNER JOIN EQUIPO e1 ON p.id_equipo1 = e1.id_equipo
              INNER JOIN EQUIPO e2 ON p.id_equipo2 = e2.id_equipo
              ORDER BY p.fecha_hora DESC";
$res_todos = mysqli_query($conexion, $sql_todos);

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

<!-- ========== SECCIÓN 2: AÑADIR JUEGOS ========== -->
<div class="card bg-dark border-secondary mb-4">
    <div class="card-header bg-dark border-secondary">
        <h5 class="mb-0 text-accent">
            <i class="bi bi-controller"></i> Añadir Juego a un Partido
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($exito_juego)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($exito_juego) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="accion" value="juego">
            <div class="row">
                <!-- Seleccionar partido -->
                <div class="col-md-3 mb-3">
                    <label for="id_partido_juego" class="form-label text-white">Partido *</label>
                    <select class="form-select bg-dark text-white border-secondary"
                            id="id_partido_juego" name="id_partido_juego" required>
                        <option value="">-- Seleccionar --</option>
                        <?php
                        mysqli_data_seek($res_todos, 0);
                        while ($pt = mysqli_fetch_assoc($res_todos)):
                        ?>
                            <option value="<?= $pt['id_partido'] ?>">
                                <?= htmlspecialchars($pt['tag1']) ?> vs <?= htmlspecialchars($pt['tag2']) ?>
                                <?= $pt['fecha_hora'] ? ' (' . date('d/m', strtotime($pt['fecha_hora'])) . ')' : '' ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- Número de juego -->
                <div class="col-md-2 mb-3">
                    <label for="numero_juego" class="form-label text-white">Nº Juego</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           id="numero_juego" name="numero_juego" min="1" value="1" required>
                </div>
                <!-- Goles equipo 1 -->
                <div class="col-md-2 mb-3">
                    <label for="goles_equipo1" class="form-label text-white">Goles Eq.1</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           id="goles_equipo1" name="goles_equipo1" min="0" value="0" required>
                </div>
                <!-- Goles equipo 2 -->
                <div class="col-md-2 mb-3">
                    <label for="goles_equipo2" class="form-label text-white">Goles Eq.2</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           id="goles_equipo2" name="goles_equipo2" min="0" value="0" required>
                </div>
                <!-- Duración -->
                <div class="col-md-2 mb-3">
                    <label for="duracion" class="form-label text-white">Duración (seg)</label>
                    <input type="number" class="form-control bg-dark text-white border-secondary"
                           id="duracion" name="duracion" min="0" value="300" required>
                </div>
                <!-- Botón -->
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-accent w-100" title="Añadir juego">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
            </div>
        </form>
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
