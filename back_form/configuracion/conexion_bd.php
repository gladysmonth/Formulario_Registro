<?php
/**
 * Conexión a la Base de Datos PostgreSQL mediante PDO
 * Compatible con PHP 7.3
 * Archivo: conexion_bd.php
 */

function obtener_conexion_bd() {
    $servidor = getenv('DB_HOST') ? getenv('DB_HOST') : 'db';
    $puerto   = getenv('DB_PORT') ? getenv('DB_PORT') : '5432';
    $base_datos = getenv('DB_NAME') ? getenv('DB_NAME') : 'formularios_db';
    $usuario  = getenv('DB_USER') ? getenv('DB_USER') : 'postgres_usuario';
    $clave    = getenv('DB_PASS') ? getenv('DB_PASS') : 'postgres_clave_secreta';

    $cadena_conexion = "pgsql:host={$servidor};port={$puerto};dbname={$base_datos};options='--client_encoding=UTF8'";

    $opciones = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 5
    );

    try {
        $conexion = new PDO($cadena_conexion, $usuario, $clave, $opciones);
        return $conexion;
    } catch (PDOException $e) {
        // En producción o desarrollo, devolver un mensaje claro
        throw new Exception("Error de conexión a PostgreSQL: " . $e->getMessage());
    }
}
