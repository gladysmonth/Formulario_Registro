<?php
/**
 * Controlador de Cierre de Sesión
 * Compatible con PHP 7.3
 * Archivo: front_forms/logout.php
 */

require_once __DIR__ . '/componentes/control_sesion.php';

destruir_sesion();

header('Location: login.php?mensaje=sesion_cerrada');
exit();
