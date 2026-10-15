const http = require('http');

// 1. Simular login de usuario PERSONAL (solicitante)
const postData = JSON.stringify({
    id: 3,
    usuario: 'personal',
    nombre_completo: 'Personal Solicitante',
    rol: 'usuario',
    departamento: 'Administración y Finanzas',
    cargo: 'Auxiliar Administrativo'
});

const req = http.request('http://localhost:82/login.php?accion=guardar_sesion', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(postData)
    }
}, res => {
    const cookies = res.headers['set-cookie'];
    const cookieHeader = cookies ? cookies.map(c => c.split(';')[0]).join('; ') : '';

    // Probar index.php como Personal Solicitante
    http.get('http://localhost:82/index.php', {
        headers: { 'Cookie': cookieHeader }
    }, resIndex => {
        let body = '';
        resIndex.on('data', c => body += c);
        resIndex.on('end', () => {
            console.log('\n--- PERSONAL SOLICITANTE: INDEX.PHP ---');
            console.log('Status:', resIndex.statusCode);
            console.log('Tiene boton menu en navbar:', body.includes('btn-abrir-menu-cosmol'));
            console.log('Tiene barraLateralCosmol:', body.includes('barraLateralCosmol'));
            console.log('Tiene enlace Soporte:', body.includes('Soporte'));
            console.log('Tiene enlace Incidencias:', body.includes('Incidencias'));
            console.log('Tiene enlace Accesos:', body.includes('Accesos'));
        });
    });
});

req.write(postData);
req.end();
