<?php
/**
 * Utilitario de Cabeceras CORS y Formato de Respuestas JSON
 * Compatible con PHP 7.3
 * Archivo: respuestas_api.php
 */

function configurar_cors() {
    // Permitir cualquier origen para desarrollo o configurar según necesidad
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Content-Type: application/json; charset=UTF-8");

    // Responder de inmediato a peticiones preliminares de preflight (OPTIONS)
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

function responder_json($datos, $codigo_http = 200) {
    http_response_code($codigo_http);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function responder_exito($mensaje, $datos = null, $codigo_http = 200) {
    $respuesta = array(
        'exito' => true,
        'mensaje' => $mensaje
    );
    if ($datos !== null) {
        $respuesta['datos'] = $datos;
    }
    responder_json($respuesta, $codigo_http);
}

function responder_error($mensaje, $codigo_http = 400, $detalles = null) {
    $respuesta = array(
        'exito' => false,
        'mensaje' => $mensaje
    );
    if ($detalles !== null) {
        $respuesta['errores'] = $detalles;
    }
    responder_json($respuesta, $codigo_http);
}

function obtener_cuerpo_json() {
    $cuerpo_crudo = file_get_contents('php://input');
    if (empty($cuerpo_crudo)) {
        return array();
    }
    $decodificado = json_decode($cuerpo_crudo, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        responder_error('El cuerpo de la solicitud no es un JSON válido', 400);
    }
    return is_array($decodificado) ? $decodificado : array();
}
