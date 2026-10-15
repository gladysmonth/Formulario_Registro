const http = require('http');

function check(url) {
    http.get(url, res => {
        let body = '';
        res.on('data', c => body += c);
        res.on('end', () => {
            console.log(url, 'Status:', res.statusCode);
            console.log('Tiene boton menu:', body.includes('btn-abrir-menu-cosmol'));
            console.log('Tiene sidebar aside:', body.includes('barraLateralCosmol'));
            console.log('Tiene backdrop:', body.includes('sidebarBackdrop'));
        });
    });
}

check('http://localhost:82/index.php');
check('http://localhost:82/metricas.php');
