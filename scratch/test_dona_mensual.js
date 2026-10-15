// Script de prueba para validar la lógica de la dona mensual y navegación de meses
const fs = require('fs');

// Simulación de registros de prueba (los 23 que tenemos en Octubre 2026)
const registrosOctubre = {
    soporte: [
        { fecha_solicitud: '2026-10-15 10:30:00' },
        { fecha_solicitud: '2026-10-14 09:00:00' },
        { fecha_solicitud: '2026-10-13 14:20:00' },
        { fecha_solicitud: '2026-10-12 11:15:00' },
        { fecha_solicitud: '2026-10-10 16:45:00' },
        { fecha_solicitud: '2026-10-08 08:30:00' },
        { fecha_solicitud: '2026-10-06 15:00:00' },
        { fecha_solicitud: '2026-10-05 10:10:00' },
        { fecha_solicitud: '2026-10-02 12:00:00' }
    ],
    incidencias: [
        { fecha_hora_reporte: '2026-10-15 11:00:00' },
        { fecha_hora_reporte: '2026-10-14 10:30:00' },
        { fecha_hora_reporte: '2026-10-12 16:00:00' },
        { fecha_hora_reporte: '2026-10-09 09:15:00' },
        { fecha_hora_reporte: '2026-10-03 14:00:00' }
    ],
    accesos: [
        { fecha_solicitud: '2026-10-15 10:00:00' },
        { fecha_solicitud: '2026-10-13 09:00:00' },
        { fecha_solicitud: '2026-10-11 15:30:00' },
        { fecha_solicitud: '2026-10-07 11:45:00' },
        { fecha_solicitud: '2026-10-04 08:30:00' }
    ],
    mantenimiento: [
        { fecha_mantenimiento: '2026-10-15' },
        { fecha_mantenimiento: '2026-10-14' },
        { fecha_mantenimiento: '2026-10-10' },
        { fecha_mantenimiento: '2026-10-05' }
    ]
};

function obtenerRangoMes(offsetMeses = 0) {
    const ahora = new Date();
    const anio = ahora.getFullYear();
    const mesIndex = ahora.getMonth() + (offsetMeses || 0);

    const inicio = new Date(anio, mesIndex, 1, 0, 0, 0, 0);
    const fin = new Date(anio, mesIndex + 1, 0, 23, 59, 59, 999);

    return { inicio, fin };
}

function calcularDona(offset) {
    const rango = obtenerRangoMes(offset);
    const nombresMeses = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    const nombreMes = nombresMeses[rango.inicio.getMonth()];
    const anio = rango.inicio.getFullYear();

    function estaEnRango(fechaStr) {
        if (!fechaStr) return false;
        const f = new Date(fechaStr.indexOf(' ') !== -1 ? fechaStr.replace(' ', 'T') : fechaStr);
        if (isNaN(f.getTime())) return false;
        return (f >= rango.inicio && f <= rango.fin);
    }

    let conteo = { sop: 0, inc: 0, acc: 0, mp: 0 };
    registrosOctubre.soporte.forEach(t => { if (estaEnRango(t.fecha_solicitud)) conteo.sop++; });
    registrosOctubre.incidencias.forEach(i => { if (estaEnRango(i.fecha_hora_reporte)) conteo.inc++; });
    registrosOctubre.accesos.forEach(a => { if (estaEnRango(a.fecha_solicitud)) conteo.acc++; });
    registrosOctubre.mantenimiento.forEach(mp => { if (estaEnRango(mp.fecha_mantenimiento)) conteo.mp++; });

    const totalMes = conteo.sop + conteo.inc + conteo.acc + conteo.mp;
    const pctSop = totalMes > 0 ? Math.round((conteo.sop / totalMes) * 100) : 0;
    const pctInc = totalMes > 0 ? Math.round((conteo.inc / totalMes) * 100) : 0;
    const pctAcc = totalMes > 0 ? Math.round((conteo.acc / totalMes) * 100) : 0;
    const pctMp  = totalMes > 0 ? Math.max(0, 100 - (pctSop + pctInc + pctAcc)) : 0;

    return {
        mes: `${nombreMes} ${anio}`,
        total: totalMes,
        soporte: `${conteo.sop} (${pctSop}%)`,
        incidencias: `${conteo.inc} (${pctInc}%)`,
        accesos: `${conteo.acc} (${pctAcc}%)`,
        mantenimiento: `${conteo.mp} (${pctMp}%)`
    };
}

console.log('=== MES ACTUAL (Offset 0): ===');
console.log(calcularDona(0));

console.log('\n=== MES ANTERIOR (Offset -1): ===');
console.log(calcularDona(-1));
