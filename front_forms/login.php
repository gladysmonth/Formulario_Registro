<?php
/**
 * Vista y Controlador: Inicio de Sesión Institucional (Login)
 * Compatible con PHP 7.3 y Bootstrap 5
 * Archivo: front_forms/login.php
 */

require_once __DIR__ . '/componentes/control_sesion.php';

// Si el usuario ya tiene sesión iniciada, redirigir a su pantalla principal según su rol
if (esta_autenticado()) {
    $rol_sesion = obtener_rol_actual();
    if (in_array($rol_sesion, array('admin', 'tecnico'))) {
        header('Location: metricas.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

// Endpoint interno para sincronizar la sesión PHP desde JavaScript (Fetch)
if (isset($_GET['accion']) && $_GET['accion'] === 'guardar_sesion') {
    header('Content-Type: application/json; charset=UTF-8');
    $cuerpo_crudo = file_get_contents('php://input');
    if ($cuerpo_crudo !== false) {
        $cuerpo_crudo = preg_replace('/^\xEF\xBB\xBF/', '', trim($cuerpo_crudo));
    }
    $datos = json_decode($cuerpo_crudo, true);
    
    if (is_array($datos) && !empty($datos['id'])) {
        establecer_sesion_usuario($datos);
        $rol = isset($datos['rol']) ? $datos['rol'] : 'usuario';
        $es_admin_tecnico = in_array($rol, array('admin', 'tecnico'));
        $destino_defecto = $es_admin_tecnico ? 'metricas.php' : 'index.php';
        
        $url_destino = isset($_SESSION['url_redireccion']) ? $_SESSION['url_redireccion'] : $destino_defecto;
        if ($es_admin_tecnico && ($url_destino === 'index.php' || empty($url_destino))) {
            $url_destino = 'metricas.php';
        }
        unset($_SESSION['url_redireccion']);
        echo json_encode(array('exito' => true, 'url_destino' => $url_destino));
    } else {
        http_response_code(400);
        echo json_encode(array(
            'exito' => false, 
            'mensaje' => 'Datos de usuario inválidos', 
            'recibido' => $cuerpo_crudo,
            'json_error' => json_last_error_msg()
        ));
    }
    exit();
}

// Mensajes recibidos por URL (ej. cierre de sesión o acceso denegado)
$mensaje_alerta = '';
$tipo_alerta = '';

if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'sesion_cerrada') {
    $mensaje_alerta = 'Has cerrado sesión correctamente. Vuelve a ingresar cuando lo necesites.';
    $tipo_alerta = 'info';
} elseif (isset($_GET['error']) && $_GET['error'] === 'acceso_denegado') {
    $mensaje_alerta = 'Acceso restringido. Por favor inicia sesión con credenciales autorizadas.';
    $tipo_alerta = 'warning';
}

// Detección dinámica del host para la API backend
$host_cliente = isset($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST'])[0] : '';
$url_backend = !empty($host_cliente) ? ('http://' . $host_cliente . ':83') : (getenv('URL_BACKEND') ?: 'http://192.168.11.46:83');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | Cosmol R.L.</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos Personalizados del Sistema -->
    <link rel="stylesheet" href="recursos/css/estilos_personalizados.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 1.5rem;
        }
        .tarjeta-login {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
        .cabecera-login {
            background: linear-gradient(135deg, #0d6efd 0%, #0052cc 100%);
            color: #ffffff;
            padding: 2.25rem 2rem 1.75rem 2rem;
            text-align: center;
        }
        .icono-escudo {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.18);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.85rem;
        }
        .cuerpo-login {
            padding: 2.25rem 2rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.18);
        }
        .input-group-text {
            background-color: #f8fafc;
            border-color: #dee2e6;
            color: #64748b;
        }
    </style>

    <script>
        window.URL_BACKEND = <?php echo json_encode($url_backend); ?>;
    </script>
</head>
<body>

<main class="tarjeta-login">
    <!-- Encabezado Institucional -->
    <header class="cabecera-login">
        <div class="icono-escudo shadow-sm">
            <i class="bi bi-shield-lock-fill fs-2"></i>
        </div>
        <h1 class="h4 fw-bold mb-1">COSMOL R.L.</h1>
        <p class="small text-white-50 mb-0">Sistema Modular de Formularios y Soporte</p>
    </header>

    <!-- Cuerpo del Formulario de Ingreso -->
    <div class="cuerpo-login">
        
        <?php if (!empty($mensaje_alerta)): ?>
            <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show d-flex align-items-center gap-2 small mb-4 shadow-sm" role="alert">
                <i class="bi <?php echo $tipo_alerta === 'info' ? 'bi-info-circle-fill' : 'bi-exclamation-triangle-fill'; ?> fs-5"></i>
                <div><?php echo htmlspecialchars($mensaje_alerta); ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>

        <!-- Alerta de Error Dinámica (JS) -->
        <div id="alertaErrorLogin" class="alert alert-danger d-none align-items-center gap-2 small mb-4 shadow-sm" role="alert">
            <i class="bi bi-x-circle-fill fs-5 flex-shrink-0"></i>
            <span id="mensajeErrorLogin">Usuario o contraseña incorrectos</span>
        </div>

        <form id="formularioLogin" autocomplete="off" novalidate>
            <!-- Campo: Nombre de Usuario -->
            <div class="mb-3">
                <label for="campoUsuario" class="form-label small fw-semibold text-secondary">
                    Usuario Institucional
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" id="campoUsuario" name="usuario" 
                           placeholder="Ej: admin, tecnico, personal" required autofocus>
                </div>
            </div>

            <!-- Campo: Contraseña -->
            <div class="mb-4">
                <label for="campoClave" class="form-label small fw-semibold text-secondary">
                    Contraseña
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                    <input type="password" class="form-control" id="campoClave" name="clave" 
                           placeholder="••••••••••••" required>
                    <button class="btn btn-outline-secondary" type="button" id="btnToggleClave" title="Ver / Ocultar contraseña">
                        <i class="bi bi-eye" id="iconoOjo"></i>
                    </button>
                </div>
            </div>

            <!-- Botón de Envío -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3 d-flex align-items-center justify-content-center gap-2" id="btnEnviarLogin">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span>Iniciar Sesión</span>
            </button>

            <!-- Nota Informativa: Asignación por Administrador -->
            <div class="text-center mb-3 text-secondary" style="font-size: 0.76rem;">
                <i class="bi bi-shield-check text-primary me-1"></i>
                Las credenciales son asignadas exclusivamente por el Administrador del Sistema.
            </div>
        </form>

        <!-- Pie de Información Institucional -->
        <footer class="text-center pt-3 border-top">
            <small class="text-muted d-block" style="font-size: 0.75rem;">
                Cooperativa de Servicios Públicos "Montero" R.L.
            </small>
            <small class="text-secondary" style="font-size: 0.7rem;">
                Departamento de Sistemas e Informática &bull; 2026
            </small>
        </footer>
    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script de Autenticación Asíncrona -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formularioLogin');
    const inputUsuario = document.getElementById('campoUsuario');
    const inputClave = document.getElementById('campoClave');
    const btnSubmit = document.getElementById('btnEnviarLogin');
    const alertaError = document.getElementById('alertaErrorLogin');
    const mensajeError = document.getElementById('mensajeErrorLogin');
    const btnToggleClave = document.getElementById('btnToggleClave');
    const iconoOjo = document.getElementById('iconoOjo');

    // Alternar visibilidad de contraseña
    btnToggleClave.addEventListener('click', () => {
        const esPassword = inputClave.type === 'password';
        inputClave.type = esPassword ? 'text' : 'password';
        iconoOjo.className = esPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
    });

    // Envío asíncrono hacia el backend
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        alertaError.classList.add('d-none');

        const usuario = inputUsuario.value.trim();
        const clave = inputClave.value;

        if (!usuario || !clave) {
            mensajeError.textContent = 'Por favor ingresa usuario y contraseña.';
            alertaError.classList.remove('d-none');
            return;
        }

        // Estado visual de carga
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verificando credenciales...';

        try {
            const urlApi = (window.URL_BACKEND || 'http://localhost:83') + '/api/auth/login.php';
            
            // 1. Validar credenciales contra la API backend de tu compañero
            const respuestaBackend = await fetch(urlApi, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ usuario: usuario, clave: clave })
            });

            const datosBackend = await respuestaBackend.json();

            if (!datosBackend.exito) {
                throw new Error(datosBackend.mensaje || 'Credenciales incorrectas');
            }

            // 2. Establecer la sesión en PHP en el frontend
            const respuestaSesion = await fetch('login.php?accion=guardar_sesion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datosBackend.datos.usuario)
            });

            const datosSesion = await respuestaSesion.json();

            if (datosSesion.exito) {
                btnSubmit.classList.remove('btn-primary');
                btnSubmit.classList.add('btn-success');
                btnSubmit.innerHTML = '<i class="bi bi-check-circle-fill"></i> ¡Acceso concedido!';
                
                setTimeout(() => {
                    window.location.href = datosSesion.url_destino || 'index.php';
                }, 500);
            } else {
                throw new Error(datosSesion.mensaje || 'Error al inicializar sesión');
            }

        } catch (error) {
            mensajeError.textContent = error.message || 'Error de conexión con el servicio de autenticación';
            alertaError.classList.remove('d-none');
            alertaError.classList.add('d-flex');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right fs-5"></i> <span>Iniciar Sesión</span>';
            inputClave.value = '';
            inputClave.focus();
        }
    });
});
</script>

</body>
</html>
