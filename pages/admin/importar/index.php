<?php
/**
 * IMPORTAR — Carga masiva de Partidos o Estadísticas desde CSV/JSON
 *
 * Formato CSV Partidos:
 *   id_torneo,id_equipo1,id_equipo2,fecha_hora,formato
 *
 * Formato CSV Estadísticas:
 *   id_partido,id_jugador,goles,asistencias,salvadas,tiros,mvp
 *
 * Formato JSON Partidos (array de objetos con las mismas claves)
 * Formato JSON Estadísticas (array de objetos con las mismas claves)
 */

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';
require_once __DIR__ . '/../../../includes/auditoria.php';

requiereRol('admin');

$resultado = null;

// ========== PROCESAR UPLOAD ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $modo   = $_POST['modo'] ?? '';
    $file   = $_FILES['archivo'];
    $ext    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $errores = [];
    $ok      = 0;
    $skip    = 0;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error al subir el archivo (código {$file['error']}).";
    } elseif (!in_array($ext, ['csv', 'json'])) {
        $errores[] = "Solo se aceptan archivos .csv o .json";
    } elseif (!in_array($modo, ['partidos', 'estadisticas'])) {
        $errores[] = "Modo no válido.";
    } else {
        $contenido = file_get_contents($file['tmp_name']);

        // --- Parsear filas ---
        $filas = [];
        if ($ext === 'json') {
            $decoded = json_decode($contenido, true);
            if (!is_array($decoded)) {
                $errores[] = "JSON inválido o no es un array.";
            } else {
                $filas = $decoded;
            }
        } else {
            // CSV: primera línea = cabeceras
            $lineas = array_filter(explode("\n", trim($contenido)));
            $cabeceras = null;
            foreach ($lineas as $linea) {
                $cols = str_getcsv(trim($linea));
                if ($cabeceras === null) {
                    $cabeceras = array_map('trim', $cols);
                    continue;
                }
                if (count($cols) !== count($cabeceras)) { $skip++; continue; }
                $filas[] = array_combine($cabeceras, array_map('trim', $cols));
            }
        }

        // --- Insertar filas ---
        if (empty($errores)) {
            foreach ($filas as $i => $fila) {
                $linea = $i + 2;

                if ($modo === 'partidos') {
                    $campos = ['id_torneo','id_equipo1','id_equipo2','fecha_hora','formato'];
                    foreach ($campos as $c) {
                        if (!array_key_exists($c, $fila)) {
                            $errores[] = "Línea $linea: falta columna '$c'.";
                            $skip++;
                            continue 2;
                        }
                    }
                    $id_torneo  = intval($fila['id_torneo']);
                    $id_eq1     = intval($fila['id_equipo1']);
                    $id_eq2     = intval($fila['id_equipo2']);
                    $fecha_hora = mysqli_real_escape_string($conexion, $fila['fecha_hora']);
                    $formato    = mysqli_real_escape_string($conexion, $fila['formato'] ?? 'BO5');

                    if (!$id_torneo || !$id_eq1 || !$id_eq2 || $id_eq1 === $id_eq2) {
                        $errores[] = "Línea $linea: datos de partido inválidos.";
                        $skip++;
                        continue;
                    }
                    $res = mysqli_query($conexion,
                        "INSERT INTO PARTIDO (id_torneo, id_equipo1, id_equipo2, fecha_hora, formato)
                         VALUES ($id_torneo, $id_eq1, $id_eq2, '$fecha_hora', '$formato')"
                    );
                    if ($res) {
                        $ok++;
                    } else {
                        $errores[] = "Línea $linea: " . mysqli_error($conexion);
                        $skip++;
                    }

                } else { // estadisticas
                    $campos = ['id_partido','id_jugador','goles','asistencias','salvadas','tiros','mvp'];
                    foreach ($campos as $c) {
                        if (!array_key_exists($c, $fila)) {
                            $errores[] = "Línea $linea: falta columna '$c'.";
                            $skip++;
                            continue 2;
                        }
                    }
                    $id_partido   = intval($fila['id_partido']);
                    $id_jugador   = intval($fila['id_jugador']);
                    $goles        = intval($fila['goles']);
                    $asistencias  = intval($fila['asistencias']);
                    $salvadas     = intval($fila['salvadas']);
                    $tiros        = intval($fila['tiros']);
                    $mvp          = intval((bool)$fila['mvp']);

                    if (!$id_partido || !$id_jugador) {
                        $errores[] = "Línea $linea: id_partido o id_jugador inválidos.";
                        $skip++;
                        continue;
                    }

                    // UPSERT
                    $check = mysqli_query($conexion,
                        "SELECT id_estadistica FROM ESTADISTICAS_JUGADOR
                         WHERE id_partido=$id_partido AND id_jugador=$id_jugador");
                    if (mysqli_num_rows($check) > 0) {
                        $eid = mysqli_fetch_assoc($check)['id_estadistica'];
                        $res = mysqli_query($conexion,
                            "UPDATE ESTADISTICAS_JUGADOR SET
                             goles=$goles, asistencias=$asistencias, salvadas=$salvadas,
                             tiros=$tiros, mvp=$mvp
                             WHERE id_estadistica=$eid");
                    } else {
                        $res = mysqli_query($conexion,
                            "INSERT INTO ESTADISTICAS_JUGADOR
                             (id_partido, id_jugador, goles, asistencias, salvadas, tiros, mvp)
                             VALUES ($id_partido, $id_jugador, $goles, $asistencias, $salvadas, $tiros, $mvp)");
                    }
                    if ($res) {
                        $ok++;
                    } else {
                        $errores[] = "Línea $linea: " . mysqli_error($conexion);
                        $skip++;
                    }
                }
            }

            registrarAuditoria($conexion, 'IMPORT', strtoupper($modo),
                null, "Archivo: {$file['name']} | OK: $ok | Errores: " . count($errores));
        }
    }

    $resultado = compact('ok', 'skip', 'errores', 'modo');
}

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-upload"></i> Importación Masiva
    </h2>
    <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Admin
    </a>
</div>

<!-- Resultado -->
<?php if ($resultado !== null): ?>
<div class="alert <?= empty($resultado['errores']) ? 'alert-success' : 'alert-warning' ?> mb-4">
    <strong><i class="bi bi-check-circle"></i> Importación completada</strong>
    &mdash; <?= $resultado['ok'] ?> filas importadas,
    <?= $resultado['skip'] ?> omitidas.
    <?php if (!empty($resultado['errores'])): ?>
    <ul class="mt-2 mb-0 small">
        <?php foreach (array_slice($resultado['errores'], 0, 10) as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
        <?php if (count($resultado['errores']) > 10): ?>
        <li class="text-muted">… y <?= count($resultado['errores']) - 10 ?> errores más.</li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Formulario -->
    <div class="col-lg-6">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent"><i class="bi bi-file-earmark-arrow-up"></i> Subir Archivo</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label text-white">Tipo de datos</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="modo" id="modoPartidos"
                                   value="partidos" checked>
                            <label class="btn btn-outline-info" for="modoPartidos">
                                <i class="bi bi-joystick"></i> Partidos
                            </label>
                            <input type="radio" class="btn-check" name="modo" id="modoStats"
                                   value="estadisticas">
                            <label class="btn btn-outline-info" for="modoStats">
                                <i class="bi bi-graph-up"></i> Estadísticas
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white">Archivo (.csv o .json)</label>
                        <input type="file" name="archivo" accept=".csv,.json"
                               class="form-control bg-dark text-white border-secondary" required>
                        <div class="form-text text-muted">Máximo 5 MB</div>
                    </div>

                    <button type="submit" class="btn btn-accent w-100">
                        <i class="bi bi-upload"></i> Importar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Guía de formato -->
    <div class="col-lg-6">
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary">
                <h5 class="mb-0 text-accent"><i class="bi bi-file-text"></i> Formato esperado</h5>
            </div>
            <div class="card-body">
                <h6 class="text-white"><i class="bi bi-joystick"></i> Partidos — CSV</h6>
                <pre class="bg-black text-success rounded p-2 small">id_torneo,id_equipo1,id_equipo2,fecha_hora,formato
7,1,2,2023-07-12 14:00:00,BO5
7,3,4,2023-07-12 16:00:00,BO5</pre>

                <h6 class="text-white mt-3"><i class="bi bi-joystick"></i> Partidos — JSON</h6>
                <pre class="bg-black text-success rounded p-2 small">[
  {"id_torneo":7,"id_equipo1":1,"id_equipo2":2,
   "fecha_hora":"2023-07-12 14:00:00","formato":"BO5"}
]</pre>

                <h6 class="text-white mt-3"><i class="bi bi-graph-up"></i> Estadísticas — CSV</h6>
                <pre class="bg-black text-success rounded p-2 small">id_partido,id_jugador,goles,asistencias,salvadas,tiros,mvp
1,5,2,1,3,6,1
1,7,0,2,4,4,0</pre>

                <div class="alert alert-secondary mt-3 small mb-0">
                    <i class="bi bi-info-circle"></i>
                    Las estadísticas hacen <strong>upsert</strong>: si ya existe la combinación
                    (id_partido + id_jugador), actualiza los valores.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
