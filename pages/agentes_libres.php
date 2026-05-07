<?php
/**
 * AGENTES LIBRES — Jugadores sin roster activo
 * Jugadores activos que no tienen ninguna entrada ROSTER con fecha_fin IS NULL
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/sesion.php';

// === LÓGICA PHP ===

$sql = "SELECT j.id_jugador, j.nickname, j.nombre_real, j.pais,
               lr.ultima_salida
        FROM JUGADOR j
        LEFT JOIN (
            SELECT id_jugador, MAX(fecha_fin) AS ultima_salida
            FROM ROSTER
            GROUP BY id_jugador
        ) lr ON j.id_jugador = lr.id_jugador
        WHERE j.activo = 1
          AND NOT EXISTS (
              SELECT 1 FROM ROSTER r
              WHERE r.id_jugador = j.id_jugador AND r.fecha_fin IS NULL
          )
        ORDER BY j.nickname ASC";

$res = mysqli_query($conexion, $sql);
$total = $res ? mysqli_num_rows($res) : 0;

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ========== TÍTULO ========== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0">
        <i class="bi bi-person-x"></i> Agentes Libres
    </h2>
    <span class="badge bg-secondary fs-6"><?= $total ?> jugador<?= $total !== 1 ? 'es' : '' ?></span>
</div>

<?php if ($total === 0): ?>
<div class="alert alert-info">
    <i class="bi bi-check-circle"></i>
    No hay jugadores activos sin equipo en este momento. Todos tienen un roster activo asignado.
</div>
<?php else: ?>

<div class="card bg-dark border-secondary">
    <div class="card-body p-0">
        <table class="table table-dark table-hover mb-0">
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Nombre Real</th>
                    <th>País</th>
                    <th>Última salida</th>
                    <th class="text-center">Perfil</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($j = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td>
                        <strong class="text-white"><?= htmlspecialchars($j['nickname']) ?></strong>
                    </td>
                    <td class="text-muted">
                        <?= htmlspecialchars($j['nombre_real'] ?? '—') ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($j['pais'] ?? '—') ?>
                    </td>
                    <td>
                        <?php if ($j['ultima_salida']): ?>
                            <span class="text-warning">
                                <i class="bi bi-calendar-x"></i>
                                <?= date('d/m/Y', strtotime($j['ultima_salida'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Sin historial</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="/RLCS/CRM/pages/jugadores/detalle.php?id=<?= $j['id_jugador'] ?>"
                           class="btn btn-sm btn-outline-info" title="Ver perfil">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
