<?php
/**
 * AUDITORIA — helper para registrar cambios en el CRM
 * Usa prepared statements. Falla silenciosamente si la tabla no existe aún.
 */

function registrarAuditoria($conexion, $accion, $tabla, $id_registro = null, $detalle = '') {
    $usuario    = $_SESSION['nombre'] ?? 'sistema';
    $id_usuario = intval($_SESSION['id_usuario'] ?? 0);
    $accion     = substr($accion, 0, 50);
    $tabla      = substr($tabla, 0, 50);
    $detalle    = substr($detalle, 0, 1000);
    $id_reg     = ($id_registro !== null) ? intval($id_registro) : null;

    $stmt = mysqli_prepare($conexion,
        "INSERT INTO AUDITORIA (usuario, id_usuario, accion, tabla, id_registro, detalle)
         VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) return;
    mysqli_stmt_bind_param($stmt, 'sissis',
        $usuario, $id_usuario, $accion, $tabla, $id_reg, $detalle);
    mysqli_stmt_execute($stmt);
}
