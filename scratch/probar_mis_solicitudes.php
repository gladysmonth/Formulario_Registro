<?php
function peticion($url, $metodo = "GET", $datos = null, $cookies = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    if ($cookies) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
    }
    if ($metodo === "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($datos !== null) {
            $json = is_string($datos) ? $datos : json_encode($datos);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        }
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($res, $header_size);
    curl_close($ch);
    return array("code" => $code, "body" => $body);
}

$cookies = __DIR__ . "/test_cookies_personal.txt";
if (file_exists($cookies)) unlink($cookies);

// 1. Unauthenticated test
$r_noauth = peticion("http://localhost:82/modulos/mis_solicitudes/mis_solicitudes.php");
echo "1. Acceso sin sesión a mis_solicitudes.php: HTTP " . $r_noauth["code"] . " (Esperado 302 a login)\n";

// 2. Login personal
$r_log = peticion("http://localhost:83/api/auth/login.php", "POST", array("usuario" => "personal", "clave" => "Cosmol2026!"));
$datos = json_decode($r_log["body"], true);
peticion("http://localhost:82/login.php?accion=guardar_sesion", "POST", $datos["datos"]["usuario"], $cookies);

// 3. Authenticated test
$r_auth = peticion("http://localhost:82/modulos/mis_solicitudes/mis_solicitudes.php", "GET", null, $cookies);
echo "2. Acceso con sesión 'personal' a mis_solicitudes.php: HTTP " . $r_auth["code"] . " (Esperado 200)\n";
echo "   Contiene 'Mis Solicitudes y Seguimiento': " . (strpos($r_auth["body"], "Mis Solicitudes y Seguimiento") !== false ? "SI" : "NO") . "\n";

// 4. Index.php test
$r_idx = peticion("http://localhost:82/index.php", "GET", null, $cookies);
echo "3. Index.php contiene botón 'Ver Historial de Mis Solicitudes': " . (strpos($r_idx["body"], "Ver Historial de Mis Solicitudes") !== false ? "SI" : "NO") . "\n";

// 5. Test API
$ch = curl_init("http://localhost:83/api/solicitudes_usuario/listar_mis_solicitudes.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Usuario-Id: 3"));
$apiRes = json_decode(curl_exec($ch), true);
curl_close($ch);
echo "4. API listar_mis_solicitudes.php: " . ($apiRes["exito"] ? "EXITO (Total: " . $apiRes["datos"]["kpis"]["total"] . ")" : "FALLO") . "\n";

if (file_exists($cookies)) unlink($cookies);
