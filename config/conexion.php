<?php
/**
 * CONEXIÓN A LA BASE DE DATOS
 * Este archivo configura y establece la conexión con MySQL.
 * Se incluye en todos los archivos que necesitan acceder a la BD.
 */

// --- Constantes de conexión ---
define('DB_HOST', 'localhost');     // Servidor de la base de datos
define('DB_USUARIO', 'root');      // Usuario de MySQL
define('DB_PASSWORD', '');         // Contraseña de MySQL (vacía en XAMPP por defecto)
define('DB_NOMBRE', 'rlcs');       // Nombre de la base de datos

// --- No mostrar errores PHP en pantalla (se guardan en log) ---
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/errores.log');

// --- Establecer conexión ---
$conexion = mysqli_connect(DB_HOST, DB_USUARIO, DB_PASSWORD, DB_NOMBRE);

// --- Comprobar si la conexión fue exitosa ---
if (!$conexion) {
    // Si falla, registrar el error y mostrar mensaje genérico
    error_log("Error de conexión a la BD: " . mysqli_connect_error());
    die("Error: No se pudo conectar a la base de datos. Contacta al administrador.");
}

// --- Establecer charset UTF-8 para caracteres especiales (ñ, acentos, etc.) ---
mysqli_set_charset($conexion, 'utf8');
?>
