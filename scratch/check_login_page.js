const http = require('http');

http.get('http://localhost:82/login.php', res => {
    let html = '';
    res.on('data', chunk => html += chunk);
    res.on('end', () => {
        console.log('HTTP Status:', res.statusCode);
        console.log('Tiene logo:', html.includes('recursos/img/logo_cosmol.png'));
        console.log('Tiene "Sistema Modular de Formularios":', html.includes('Sistema Modular de Formularios'));
        console.log('Tiene "Usuario:":', html.includes('Usuario:'));
        console.log('Tiene "Contraseña:":', html.includes('Contraseña:'));
        console.log('Tiene botón "INICIAR SESIÓN":', html.includes('INICIAR SESIÓN'));
        console.log('Tiene nota de credenciales:', html.includes('Las credenciales son asignadas exclusivamente por el Administrador del Sistema.'));
        console.log('Tiene pie "Montero" R.L.:', html.includes('Cooperativa de Servicios Públicos "Montero" R.L.'));
        console.log('No tiene departamento TI en footer:', !html.includes('Departamento de Sistemas e Informática'));
    });
});
