const http = require('http');

const data = JSON.stringify({ usuario: 'admin', clave: 'Cosmol2026!' });

const req = http.request('http://localhost:83/api/auth/login.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(data)
    }
}, res => {
    let body = '';
    res.on('data', chunk => body += chunk);
    res.on('end', () => {
        console.log('Login API Status:', res.statusCode);
        console.log('Login API Response:', body);
    });
});

req.on('error', e => console.error(e));
req.write(data);
req.end();
