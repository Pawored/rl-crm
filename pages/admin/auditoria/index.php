<?php
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../includes/sesion.php';

requiereRol('admin');

$por_pagina = 30;
$pagina     = max(1, intval($_GET['pagina'] ?? 1));
$offset     = ($pagina - 1) * $por_pagina;

$filtro_tabla   = mysqli_real_escape_string($conexion, $_GET['tabla']   ?? '');
$filtro_accion  = mysqli_real_escape_string($conexion, $_GET['accion']  ?? '');
$filtro_usuario = mysqli_real_escape_string($conexion, $_GET['usuario'] ?? '');

$where = [];
if ($filtro_tabla)   $where[] = "tabla   = '$filtro_tabla'";
if ($filtro_accion)  $where[] = "accion  = '$filtro_accion'";
if ($filtro_usuario) $where[] = "usuario LIKE '%$filtro_usuario%'";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = mysqli_fetch_row(mysqli_query($conexion,
    "SELECT COUNT(*) FROM AUDITORIA $where_sql"))[0] ?? 0;
$total_paginas = max(1, ceil($total / $por_pagina));

$registros = mysqli_query($conexion,
    "SELECT * FROM AUDITORIA $where_sql
     ORDER BY timestamp DESC
     LIMIT $offset, $por_pagina");

// Valores únicos para filtros
$tablas_res  = mysqli_query($conexion, "SELECT DISTINCT tabla  FROM AUDITORIA ORDER BY tabla");
$acciones_res = mysqli_query($conexion, "SELECT DISTINCT accion FROM AUDITORIA ORDER BY accion");

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-white mb-0"><i class="bi bi-journal-text"></i> Registro de Auditoría</h2>
    <div class="d-flex gap-2">
        <a href="/RLCS/CRM/pages/admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Admin
        </a>
        <?php if ($total > 0 && tieneRol('admin')): ?>
        <form method="POST" class="d-inline">
            <input type="hidden" name="accion" value="limpiar">
            <button type="submit" class="btn btn-outline-danger btn-sm"
                    onclick="return confirm('¿Borrar todo el registro de auditoría?')">
                <i class="bi bi-trash"></i> Limpiar log
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'limpiar') {
    mysqli_query($conexion, "DELETE FROM AUDITORIA");
    $_SESSION['mensaje_exito'] = "Log de auditoría limpiado.";
    header("Location: /RLCS/CRM/pages/admin/auditoria/index.php");
    exit();
}
?>

<!-- Filtros -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-auto">
        <select name="tabla" class="form-select form-select-sm bg-dark text-white border-secondary">
            <option value="">Todas las tablas</option>
            <?php while ($t = mysqli_fetch_assoc($tablas_res)): ?>
            <option value="<?= htmlspecialchars($t['tabla']) ?>"
                    <?= $filtro_tabla === $t['tabla'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['tabla']) ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="accion" class="form-select form-select-sm bg-dark text-white border-secondary">
            <option value="">Todas las acciones</option>
            <?php while ($a = mysqli_fetch_assoc($acciones_res)): ?>
            <option value="<?= htmlspecialchars($a['accion']) ?>"
                    <?= $filtro_accion === $a['accion'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['accion']) ?>
            </option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-auto">
        <input type="text" name="usuario" placeholder="Usuario..."
               class="form-control form-control-sm bg-dark text-white border-secondary"
               value="<?= htmlspecialchars($filtro_usuario) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-outline-info">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="/RLCS/CRM/pages/admin/auditoria/index.php"
           class="btn btn-sm btn-outline-secondary ms-1">
            <i class="bi bi-x"></i> Limpiar
        </a>
    </div>
</form>

<div class="d-flex justify-content-between align-items-center mb-2">
    <small class="text-muted"><?= $total ?> registro(s)</small>
</div>

<div class="table-responsive">
    <table class="table table-dark table-hover align-middle" style="font-size:.875rem">
        <thead>
            <tr>
                <th>Fecha/Hora</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Tabla</th>
                <th>ID Reg.</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($registros && mysqli_num_rows($registros) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($registros)): ?>
            <?php
            $accion_badge = match($r['accion']) {
                'INSERT'   => 'bg-success',
                'UPDATE'   => 'bg-warning text-dark',
                'DELETE'   => 'bg-danger',
                'IMPORT'   => 'bg-info text-dark',
                'CALCULAR' => 'bg-primary',
                default    => 'bg-secondary'
            };
            ?>
            <tr>
                <td class="text-muted text-nowrap">
                    <?= date('d/m/Y H:i:s', strtotime($r['timestamp'])) ?>
                </td>
                <td>
                    <i class="bi bi-person-circle text-muted"></i>
                    <?= htmlspecialchars($r['usuario']) ?>
                </td>
                <td>
                    <span class="badge <?= $accion_badge ?>">
                        <?= htmlspecialchars($r['accion']) ?>
                    </span>
                </td>
                <td><code class="text-accent"><?= htmlspecialchars($r['tabla']) ?></code></td>
                <td class="text-muted"><?= $r['id_registro'] ?? '—' ?></td>
                <td class="text-muted" style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                    title="<?= htmlspecialchars($r['detalle'] ?? '') ?>">
                    <?= htmlspecialchars(mb_strimwidth($r['detalle'] ?? '', 0, 80, '…')) ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="bi bi-journal-x"></i> Sin registros de auditoría.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_paginas > 1): ?>
<nav>
    <ul class="pagination justify-content-center mt-3">
        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina-1 ?>&tabla=<?= urlencode($filtro_tabla) ?>&accion=<?= urlencode($filtro_accion) ?>&usuario=<?= urlencode($filtro_usuario) ?>">
                &laquo;
            </a>
        </li>
        <?php for ($i = max(1,$pagina-2); $i <= min($total_paginas,$pagina+2); $i++): ?>
        <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
            <a class="page-link <?= $i === $pagina ? 'bg-accent border-accent' : 'bg-dark text-white border-secondary' ?>"
               href="?pagina=<?= $i ?>&tabla=<?= urlencode($filtro_tabla) ?>&accion=<?= urlencode($filtro_accion) ?>&usuario=<?= urlencode($filtro_usuario) ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
            <a class="page-link bg-dark text-white border-secondary"
               href="?pagina=<?= $pagina+1 ?>&tabla=<?= urlencode($filtro_tabla) ?>&accion=<?= urlencode($filtro_accion) ?>&usuario=<?= urlencode($filtro_usuario) ?>">
                &raquo;
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
