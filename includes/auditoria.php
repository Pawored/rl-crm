<?php
/**
 * AUDITORIA — helper para registrar cambios en el CRM
 * Falla silenciosamente si la tabla no existe aún.
 */

function registrarAuditoria($conexion, $accion, $tabla, $id_registro = null, $detalle = '') {
    $usuario    = mysqli_real_escape_string($conexion, $_SESSION['nombre'] ?? 'sistema');
    $id_usuario = intval($_SESSION['id_usuario'] ?? 0);
    $accion     = mysqli_real_escape_string($conexion, $accion);
    $tabla      = mysqli_real_escape_string($conexion, $tabla);
    $id_reg_sql = ($id_registro !== null) ? intval($id_registro) : 'NULL';
    $detalle    = mysqli_real_escape_string($conexion, substr($detalle, 0, 1000));

    mysqli_query($conexion,
        "INSERT INTO AUDITORIA (usuario, id_usuario, accion, tabla, id_registro, detalle)
         VALUES ('$usuario', $id_usuario, '$accion', '$tabla', $id_reg_sql, '$detalle')"
    );
}
