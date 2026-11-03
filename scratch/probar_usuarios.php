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

$cookies_admin = __DIR__ . "/test_cookies_admin.txt";
$cookies_user = __DIR__ . "/test_cookies_user.txt";
if (file_exists($cookies_admin)) unlink($cookies_admin);
if (file_exists($cookies_user)) unlink($cookies_user);

// 1. Login admin
$r_log = peticion("http://localhost:83/api/auth/login.php", "POST", array("usuario" => "admin", "clave" => "Cosmol2026!"));
$datos_adm = json_decode($r_log["body"], true);
peticion("http://localhost:82/login.php?accion=guardar_sesion", "POST", $datos_adm["datos"]["usuario"], $cookies_admin);

// 2. Probar acceso a gestion_usuarios.php como admin
$r_admin_view = peticion("http://localhost:82/modulos/usuarios/gestion_usuarios.php", "GET", null, $cookies_admin);
echo "1. Acceso gestion_usuarios.php (Admin): HTTP " . $r_admin_view["code"] . " (Esperado 200)\n";
echo "   Contiene 'Gestión de Usuarios y Roles': " . (strpos($r_admin_view["body"], "Gestión de Usuarios y Roles") !== false ? "SI" : "NO") . "\n";

// 3. Login personal
$r_log2 = peticion("http://localhost:83/api/auth/login.php", "POST", array("usuario" => "personal", "clave" => "Cosmol2026!"));
$datos_usr = json_decode($r_log2["body"], true);
peticion("http://localhost:82/login.php?accion=guardar_sesion", "POST", $datos_usr["datos"]["usuario"], $cookies_user);

// 4. Probar acceso a gestion_usuarios.php como personal
$r_usr_view = peticion("http://localhost:82/modulos/usuarios/gestion_usuarios.php", "GET", null, $cookies_user);
echo "2. Acceso gestion_usuarios.php (Personal): HTTP " . $r_usr_view["code"] . " (Esperado 302 Redirección por acceso denegado)\n";

// 5. Probar API Crear Usuario desde Admin
$ch = curl_init("http://localhost:83/api/usuarios/guardar_usuario.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    "usuario" => "usuarioprueba",
    "clave" => "Cosmol2026!",
    "nombre_completo" => "Usuario de Prueba",
    "rol" => "usuario",
    "departamento" => "Sistemas",
    "cargo" => "Asistente",
    "activo" => true
)));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-Usuario-Id: 1"));
$res_crear = json_decode(curl_exec($ch), true);
curl_close($ch);
echo "3. API Crear Usuario: " . ($res_crear["exito"] ? "EXITO" : "FALLO: " . $res_crear["mensaje"]) . "\n";
$nuevo_id = isset($res_crear["datos"]["id"]) ? $res_crear["datos"]["id"] : 0;

// 6. Probar API Cambiar Estado
$ch = curl_init("http://localhost:83/api/usuarios/cambiar_estado.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("id" => $nuevo_id, "activo" => false)));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-Usuario-Id: 1"));
$res_estado = json_decode(curl_exec($ch), true);
curl_close($ch);
echo "4. API Desactivar Usuario: " . ($res_estado["exito"] ? "EXITO" : "FALLO") . "\n";

// 7. Limpiar usuario de prueba en BD
if ($nuevo_id > 0) {
    shell_exec("docker exec contenedor_db_formularios psql -U postgres_usuario -d formularios_db -c \"DELETE FROM usuarios WHERE id = $nuevo_id; DELETE FROM auditoria_operaciones WHERE modulo = 'usuarios';\"");
    echo "5. Limpieza de usuario de prueba completada.\n";
}

if (file_exists($cookies_admin)) unlink($cookies_admin);
if (file_exists($cookies_user)) unlink($cookies_user);
