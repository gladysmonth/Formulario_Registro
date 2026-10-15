const http = require('http');

function get(url) {
    return new Promise((resolve, reject) => {
        http.get(url, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => {
                try {
                    resolve(JSON.parse(data));
                } catch(e) {
                    resolve(data);
                }
            });
        }).on('error', reject);
    });
}

async function simularDashboard() {
    const [resSoporte, resIncidencias, resAccesos, resMantenimiento, resDiag] = await Promise.all([
        get('http://localhost:83/api/soporte_tecnico/listar_tickets.php?limite=50'),
        get('http://localhost:83/api/registro_incidencias/listar_incidencias.php?limite=50'),
        get('http://localhost:83/api/asignacion_accesos/listar_solicitudes.php?limite=50'),
        get('http://localhost:83/api/mantenimiento_preventivo/listar_mantenimientos.php?limite=50'),
        get('http://localhost:83/api/diagnostico/listar.php?limite=50')
    ]);

    let totales = { soporte: 0, incidencias: 0, accesos: 0, mantenimiento: 0 };
    let pendientesPorModulo = { sop: 0, inc: 0, acc: 0 };
    let conteoDepartamentos = {};
    let conteoDiasSemana = {
        'Lunes': 0,
        'Martes': 0,
        'Miércoles': 0,
        'Jueves': 0,
        'Viernes': 0
    };
    let desgloseDiasSemana = {
        'Lunes': { sop: 0, inc: 0, acc: 0, mp: 0 },
        'Martes': { sop: 0, inc: 0, acc: 0, mp: 0 },
        'Miércoles': { sop: 0, inc: 0, acc: 0, mp: 0 },
        'Jueves': { sop: 0, inc: 0, acc: 0, mp: 0 },
        'Viernes': { sop: 0, inc: 0, acc: 0, mp: 0 }
    };

    let totalAbiertos = 0;
    let totalAbiertosHoy = 0;
    let totalCriticos = 0;
    let totalResueltos = 0;

    let hayAlertaCritica = false;
    let primerIncidenciaCritica = null;

    const ahora = new Date();
    const hoyIso = ahora.toISOString().substring(0, 10);

    function registrarFecha(fechaStr, estado, esCritico, claveModulo) {
        if (!fechaStr) return;
        const f = new Date(fechaStr.indexOf(' ') !== -1 ? fechaStr.replace(' ', 'T') : fechaStr);
        if (isNaN(f.getTime())) return;

        const diaNum = f.getDay();
        const diasMap = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        if (diaNum >= 1 && diaNum <= 5) {
            const diaNombre = diasMap[diaNum];
            conteoDiasSemana[diaNombre]++;
            if (claveModulo && desgloseDiasSemana[diaNombre] && desgloseDiasSemana[diaNombre][claveModulo] !== undefined) {
                desgloseDiasSemana[diaNombre][claveModulo]++;
            }
        }

        const fechaSolo = fechaStr.substring(0, 10);
        const esHoy = (fechaSolo === hoyIso);

        const est = (estado || '').toLowerCase();
        const esCerrado = (est === 'resuelto' || est === 'atendido' || est === 'completado' || est === 'cerrada');

        if (!esCerrado) {
            totalAbiertos++;
            if (esHoy) totalAbiertosHoy++;
            if (esCritico) totalCriticos++;
            if (claveModulo && pendientesPorModulo[claveModulo] !== undefined) {
                pendientesPorModulo[claveModulo]++;
            }
        } else {
            totalResueltos++;
        }
    }

    function registrarDemandaArea(areaTexto) {
        if (!areaTexto || typeof areaTexto !== 'string') return;
        let limpia = areaTexto.trim();
        if (!limpia || limpia.length < 2) return;
        limpia = limpia.charAt(0).toUpperCase() + limpia.slice(1);
        conteoDepartamentos[limpia] = (conteoDepartamentos[limpia] || 0) + 1;
    }

    // 1. Soporte
    if (resSoporte.exito && resSoporte.datos) {
        const d = resSoporte.datos;
        totales.soporte = parseInt(d.metricas.total, 10) || 0;
        d.tickets.forEach(t => {
            const esUrgente = (t.prioridad === 'urgente' || t.prioridad === 'alta');
            registrarFecha(t.fecha_solicitud || t.creado_en, t.estado, esUrgente, 'sop');
            registrarDemandaArea(t.departamento_area);
        });
    }

    // 2. Incidencias
    if (resIncidencias.exito && resIncidencias.datos) {
        const d = resIncidencias.datos;
        totales.incidencias = parseInt(d.metricas.total, 10) || 0;
        d.registros.forEach(i => {
            const critStr = (i.nivel_criticidad || '').toLowerCase();
            const esCrit = (critStr === 'critica_nivel_1' || critStr === 'alta_nivel_2');
            const fecha = i.fecha_hora_reporte || i.creado_en || '';
            const est = (i.estado || '').toLowerCase();
            const noCerrada = (est !== 'resuelta' && est !== 'cerrada');

            if (esCrit && noCerrada && !primerIncidenciaCritica) {
                hayAlertaCritica = true;
                primerIncidenciaCritica = {
                    codigo: i.nro_incidencia || 'INC-CRÍTICA',
                    sistema: i.sistema_erp_sai ? 'ERP - SAI (Facturación)' : (i.sistema_cobranzas_netcob ? 'Cobranzas - NETCOB' : (i.sistema_otros_detalle || 'Plataforma Crítica'))
                };
            }

            registrarFecha(fecha, i.estado, esCrit, 'inc');
            const areaInc = i.sistema_erp_sai ? 'ERP - SAI (Facturación)' : (i.sistema_cobranzas_netcob ? 'Cobranzas - NETCOB' : (i.sistema_otros_detalle || i.responsable_reporte || 'Infraestructura TI'));
            registrarDemandaArea(areaInc);
        });
    }

    // 3. Accesos
    if (resAccesos.exito && resAccesos.datos) {
        const d = resAccesos.datos;
        totales.accesos = parseInt(d.metricas.total, 10) || 0;
        d.registros.forEach(a => {
            registrarFecha(a.fecha_solicitud || a.creado_en, a.estado, false, 'acc');
            registrarDemandaArea(a.area_departamento || 'Institucional');
        });
    }

    // 4. Mantenimiento Preventivo
    if (resMantenimiento.exito && resMantenimiento.datos) {
        const d = resMantenimiento.datos;
        totales.mantenimiento = parseInt(d.metricas.total || d.metricas.total_mantenimientos, 10) || 0;
        d.registros.forEach(mp => {
            registrarFecha(mp.fecha_mantenimiento || mp.creado_en, 'completado', false, 'mp');
            registrarDemandaArea(mp.ubicacion_equipo || 'Oficinas / Equipos');
        });
    }

    console.log('==============================================');
    console.log('--- REFINAMIENTO DEL DASHBOARD DE GESTIÓN TÉCNICA ---');
    console.log('==============================================');
    console.log('1. BLOQUE A - KPIS:');
    console.log(`   - KPI 1: Carga de Trabajo = ${totalAbiertos} pendientes`);
    console.log(`     Desglose: ${pendientesPorModulo.sop} de Soporte • ${pendientesPorModulo.inc} de Incidencias • ${pendientesPorModulo.acc} de Accesos`);
    console.log(`   - KPI 2: Criticidad Alta = ${totalCriticos} urgentes/críticos`);
    console.log(`   - KPI 3: Tickets Resueltos / Atendidos = ${totalResueltos} resueltos con éxito`);
    console.log(`   - KPI 4: Mantenimientos Preventivos Realizados = ${totales.mantenimiento} fichas técnicas OK`);
    console.log('');
    console.log('2. BANNER DE ALERTA:');
    if (hayAlertaCritica && primerIncidenciaCritica) {
        console.log(`   - ESTADO: ALERTA CRÍTICA ACTIVA`);
        console.log(`   - TEXTO: Incidencia Crítica [${primerIncidenciaCritica.codigo}] en ${primerIncidenciaCritica.sistema}`);
        console.log(`   - BOTÓN: [Ir a atender] -> modulos/registro_incidencias/gestion_incidencias.php`);
    } else {
        console.log(`   - ESTADO: OPERATIVO Y ESTABLE`);
    }
    console.log('');
    console.log('3. BLOQUE B - CARGA SEMANAL (LUNES A VIERNES):');
    for (let dia in desgloseDiasSemana) {
        const d = desgloseDiasSemana[dia];
        const tot = conteoDiasSemana[dia];
        console.log(`   - ${dia} (${tot} tickets): ${d.sop} Soporte • ${d.inc} Incidencias • ${d.acc} Accesos • ${d.mp} Mantenimiento`);
    }
    console.log('');
    console.log('4. BLOQUE B - TOP DEPARTAMENTOS:');
    const sortedDeptos = Object.keys(conteoDepartamentos).map(k => ({ nombre: k, total: conteoDepartamentos[k] })).sort((a,b) => b.total - a.total);
    sortedDeptos.slice(0, 5).forEach((dep, idx) => {
        console.log(`   #${idx+1} ${dep.nombre}: ${dep.total} tickets`);
    });
    console.log(`   Área con mayor requerimiento: ${sortedDeptos[0].nombre} (${sortedDeptos[0].total} tickets)`);
    console.log('');
    console.log('5. BLOQUE B - DISTRIBUCIÓN POR FORMULARIO INSTITUCIONAL:');
    const totalGlobal = totales.soporte + totales.incidencias + totales.accesos + totales.mantenimiento;
    console.log(`   - Total registros: ${totalGlobal}`);
    console.log(`   - Formulario 1: Soporte Técnico = ${totales.soporte} tickets (${Math.round((totales.soporte/totalGlobal)*100)}%)`);
    console.log(`   - Formulario 2: Incidencias = ${totales.incidencias} tickets (${Math.round((totales.incidencias/totalGlobal)*100)}%)`);
    console.log(`   - Formulario 3: Accesos = ${totales.accesos} tickets (${Math.round((totales.accesos/totalGlobal)*100)}%)`);
    console.log(`   - Formulario 4: Mantenimiento = ${totales.mantenimiento} tickets (${Math.round((totales.mantenimiento/totalGlobal)*100)}%)`);
}

simularDashboard();
