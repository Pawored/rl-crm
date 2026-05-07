<?php
/**
 * DB — helpers para prepared statements con MySQLi
 *
 * Uso básico:
 *   $rows = db_fetch_all($conexion, "SELECT * FROM T WHERE col = ?", "s", $val);
 *   $row  = db_fetch_one($conexion, "SELECT * FROM T WHERE id = ?",  "i", $id);
 *   $stmt = db_run($conexion, "INSERT INTO T (a,b) VALUES (?,?)", "si", $str, $int);
 *   if ($stmt && $stmt->affected_rows > 0) { $newId = $stmt->insert_id; }
 *
 * Tipos para bind_param: i=int, d=double, s=string, b=blob
 */

function db_run($conexion, $sql, string $types = '', ...$params): mysqli_stmt|false
{
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) return false;
    if ($types !== '' && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return $stmt;
}

function db_fetch_all($conexion, $sql, string $types = '', ...$params): array
{
    $stmt = db_run($conexion, $sql, $types, ...$params);
    if (!$stmt) return [];
    $result = mysqli_stmt_get_result($stmt);
    $rows   = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function db_fetch_one($conexion, $sql, string $types = '', ...$params): array|null
{
    $rows = db_fetch_all($conexion, $sql, $types, ...$params);
    return $rows[0] ?? null;
}
