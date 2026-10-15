const http = require('http');

// 1. Simular login guardando sesion en login.php?accion=guardar_sesion
const postData = JSON.stringify({
    id: 1,
    usuario: 'admin',
    nombre_completo: 'Administrador de Sistemas',
    rol: 'admin',
    departamento: 'Sistemas e Informática',
    cargo: 'Jefe de Sistemas'
});

const req = http.request('http://localhost:82/login.php?accion=guardar_sesion', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(postData)
    }
}, res => {
    const cookies = res.headers['set-cookie'];
    console.log('Set-Cookie recibido:', cookies);

    const cookieHeader = cookies ? cookies.map(c => c.split(';')[0]).join('; ') : '';

    // 2. Probar index.php con sesion
    http.get('http://localhost:82/index.php?ver_catalogo=1', {
        headers: { 'Cookie': cookieHeader }
    }, resIndex => {
        let body = '';
        resIndex.on('data', c => body += c);
        resIndex.on('end', () => {
            console.log('\n--- INDEX.PHP ---');
            console.log('Status:', resIndex.statusCode);
            console.log('Tiene boton menu en navbar:', body.includes('btn-abrir-menu-cosmol'));
            console.log('Tiene barraLateralCosmol:', body.includes('barraLateralCosmol'));
            console.log('Tiene sidebarBackdrop:', body.includes('sidebarBackdrop'));
        });
    });

    // 3. Probar metricas.php con sesion
    http.get('http://localhost:82/metricas.php', {
        headers: { 'Cookie': cookieHeader }
    }, resMetricas => {
        let body = '';
        resMetricas.on('data', c => body += c);
        resMetricas.on('end', () => {
            console.log('\n--- METRICAS.PHP ---');
            console.log('Status:', resMetricas.statusCode);
            console.log('Tiene boton menu en navbar:', body.includes('btn-abrir-menu-cosmol'));
            console.log('Tiene barraLateralCosmol:', body.includes('barraLateralCosmol'));
            console.log('Tiene sidebarBackdrop:', body.includes('sidebarBackdrop'));
        });
    });
});

req.write(postData);
req.end();
