<?php
/**
 * Componente: Pie de Página y Carga de Scripts
 * Compatible con PHP 7.3
 * Archivo: pie_pagina.php
 */
$ruta_base = isset($nivel_ruta) ? $nivel_ruta : '';
?>
<footer class="bg-white border-top mt-auto py-3 text-secondary text-center small">
    <div class="container-fluid px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div>
            <strong>Sistema de Formularios de Registro</strong> &bull; Cosmol &copy; <?php echo date('Y'); ?>
        </div>
        <div class="text-muted">
            Arquitectura Modular &bull; PHP 7.3 &bull; PostgreSQL &bull; Bootstrap 5
        </div>
    </div>
</footer>

<!-- Toast Container para Notificaciones Flotantes -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="toastNotificacion" class="toast align-items-center text-white bg-primary border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i id="toastIcono" class="bi bi-info-circle fs-5"></i>
                <span id="toastMensaje">Notificación del sistema</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- SweetAlert2 para Modales de Éxito y Confirmación -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Cliente API Centralizado -->
<script src="<?php echo $ruta_base; ?>recursos/js/cliente_api.js"></script>

<?php if (isset($scripts_adicionales) && is_array($scripts_adicionales)): ?>
    <?php foreach ($scripts_adicionales as $script): ?>
        <script src="<?php echo $ruta_base . $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
