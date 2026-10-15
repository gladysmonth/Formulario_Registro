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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            padding: 1.25rem;
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .tarjeta-login {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.08);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .logo-login {
            max-height: 54px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.04));
        }
        .cuerpo-login {
            padding: 1.5rem 2rem 2rem 2rem;
        }
        .form-control {
            border-radius: 8px;
            font-size: 0.92rem;
            padding: 0.6rem 0.75rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.18);
        }
        .input-group-text {
            background-color: #f8fafc;
            border-color: #dee2e6;
            color: #64748b;
            border-radius: 8px 0 0 8px;
        }
        .btn-primary {
            border-radius: 8px;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.35);
        }

        /* Optimización Responsive para Móviles y Teléfonos */
        @media (max-width: 576px) {
            body {
                padding: 0.85rem;
            }
            .tarjeta-login {
                border-radius: 16px;
                max-width: 100%;
            }
            .cuerpo-login {
                padding: 1.25rem 1.25rem 1.5rem 1.25rem;
            }
            .logo-login {
                max-height: 46px;
            }
        }
    </style>

    <script>
        window.URL_BACKEND = <?php echo json_encode($url_backend); ?>;
    </script>
</head>
<body>

<main class="tarjeta-login">
    <!-- Encabezado con Logo Oficial de COSMOL -->
    <header class="text-center pt-4 pb-2 px-3">
        <div class="mb-2 d-flex justify-content-center align-items-center">
            <img src="recursos/img/logo_cosmol.png" alt="COSMOL R.L." class="img-fluid logo-login">
        </div>
        <h1 class="h6 text-secondary fw-semibold mb-0" style="font-size: 0.95rem; letter-spacing: 0.2px;">
            Sistema Modular de Formularios
        </h1>
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
                    Usuario:
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" id="campoUsuario" name="usuario" 
                           placeholder="Ingresa tu usuario" required autofocus>
                </div>
            </div>

            <!-- Campo: Contraseña -->
            <div class="mb-4">
                <label for="campoClave" class="form-label small fw-semibold text-secondary">
                    Contraseña:
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

            <!-- Botón de Envío: INICIAR SESIÓN -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3 d-flex align-items-center justify-content-center gap-2" id="btnEnviarLogin" style="letter-spacing: 0.5px;">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span>INICIAR SESIÓN</span>
            </button>

            <!-- Nota Informativa: Asignación por Administrador -->
            <div class="text-center mb-3 text-secondary" style="font-size: 0.76rem;">
                <i class="bi bi-shield-check text-primary me-1"></i>
                Las credenciales son asignadas exclusivamente por el Administrador del Sistema.
            </div>
        </form>

        <!-- Pie Institucional -->
        <footer class="text-center pt-3 border-top">
            <small class="text-muted d-block fw-medium" style="font-size: 0.75rem;">
                Cooperativa de Servicios Públicos "Montero" R.L.
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
            btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right fs-5"></i> <span>INICIAR SESIÓN</span>';
            inputClave.value = '';
            inputClave.focus();
        }
    });
});
</script>

</body>
</html>
