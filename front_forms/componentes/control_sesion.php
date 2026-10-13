<?php
/**
 * Componente: Helper de Control de Sesiones y Roles (RBAC)
 * Compatible con PHP 7.3
 * Archivo: front_forms/componentes/control_sesion.php
 */

// Iniciar sesión PHP si aún no ha sido iniciada en este hilo de ejecución
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Comprueba si hay un usuario con sesión activa en el frontend
 * @return bool
 */
function esta_autenticado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Retorna los datos del usuario en sesión o un array vacío si no existe
 * @return array
 */
function obtener_usuario_sesion() {
    if (!esta_autenticado()) {
        return array();
    }
    return array(
        'id'              => isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0,
        'usuario'         => isset($_SESSION['usuario_login']) ? $_SESSION['usuario_login'] : '',
        'nombre_completo' => isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : '',
        'rol'             => isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : 'usuario',
        'departamento'    => isset($_SESSION['usuario_departamento']) ? $_SESSION['usuario_departamento'] : '',
        'cargo'           => isset($_SESSION['usuario_cargo']) ? $_SESSION['usuario_cargo'] : ''
    );
}

/**
 * Retorna el rol del usuario conectado ('admin', 'tecnico', 'usuario' o vacío)
 * @return string
 */
function obtener_rol_actual() {
    return isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : '';
}

/**
 * Verifica si el usuario actual tiene alguno de los roles permitidos
 * @param array $roles_permitidos Lista de roles válidos, ej: array('admin', 'tecnico')
 * @return bool
 */
function tiene_rol($roles_permitidos) {
    if (!esta_autenticado()) {
        return false;
    }
    $rol_actual = obtener_rol_actual();
    return in_array($rol_actual, $roles_permitidos);
}

/**
 * Helper: Verifica si el usuario es Administrador
 * @return bool
 */
function es_admin() {
    return tiene_rol(array('admin'));
}

/**
 * Helper: Verifica si el usuario es Técnico o Administrador
 * @return bool
 */
function es_personal_tecnico() {
    return tiene_rol(array('admin', 'tecnico'));
}

/**
 * Helper: Verifica si el usuario es Solicitante / Usuario común
 * @return bool
 */
function es_solicitante() {
    return tiene_rol(array('usuario'));
}

/**
 * Registra los datos de usuario en la sesión al iniciar sesión exitosamente
 * @param array $datos_usuario Datos devueltos por el backend
 */
function establecer_sesion_usuario($datos_usuario) {
    $_SESSION['usuario_id']           = isset($datos_usuario['id']) ? (int)$datos_usuario['id'] : 0;
    $_SESSION['usuario_login']        = isset($datos_usuario['usuario']) ? $datos_usuario['usuario'] : '';
    $_SESSION['usuario_nombre']       = isset($datos_usuario['nombre_completo']) ? $datos_usuario['nombre_completo'] : '';
    $_SESSION['usuario_rol']          = isset($datos_usuario['rol']) ? $datos_usuario['rol'] : 'usuario';
    $_SESSION['usuario_departamento'] = isset($datos_usuario['departamento']) ? $datos_usuario['departamento'] : '';
    $_SESSION['usuario_cargo']        = isset($datos_usuario['cargo']) ? $datos_usuario['cargo'] : '';
    $_SESSION['inicio_sesion_tiempo'] = time();
}

/**
 * Obliga a que el usuario esté autenticado. Si no lo está, lo redirige al login.
 * @param string $ruta_base Ruta relativa al root de front_forms (ej: '../../' o '')
 */
function requerir_autenticacion($ruta_base = '') {
    if (!esta_autenticado()) {
        if (isset($_SERVER['REQUEST_URI'])) {
            $_SESSION['url_redireccion'] = $_SERVER['REQUEST_URI'];
        }
        header('Location: ' . $ruta_base . 'login.php');
        exit();
    }
}

/**
 * Obliga a que el usuario tenga un rol específico. Si no lo tiene, redirige al inicio.
 * @param array $roles_permitidos Lista de roles permitidos
 * @param string $ruta_base Ruta relativa al root de front_forms
 */
function requerir_rol($roles_permitidos, $ruta_base = '') {
    requerir_autenticacion($ruta_base);
    if (!tiene_rol($roles_permitidos)) {
        header('Location: ' . $ruta_base . 'index.php?error=acceso_denegado');
        exit();
    }
}

/**
 * Cierra la sesión activa destruyendo datos y cookies
 */
function destruir_sesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}
