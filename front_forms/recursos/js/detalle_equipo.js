/**
 * Lógica Cliente: Radiografía Técnica Detallada de Equipo (Diagnóstico)
 * Archivo: front_forms/recursos/js/detalle_equipo.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const macParam = params.get('mac') || '';
    const idParam = params.get('id') || '';

    const cargando = document.getElementById('cargandoDetalle');
    const contenido = document.getElementById('contenidoDetalle');
    const iconoRecarga = document.getElementById('iconoRecarga');
    const btnRecargar = document.getElementById('btnRecargar');

    const urlBackendBase = window.URL_BACKEND || 'http://' + window.location.hostname + ':83';
    let urlApiDetalle = urlBackendBase + '/api/diagnostico/detalle.php';
    if (macParam) {
        urlApiDetalle += '?mac=' + encodeURIComponent(macParam);
    } else if (idParam) {
        urlApiDetalle += '?id=' + encodeURIComponent(idParam);
    }

    async function cargarDetalle() {
        if (iconoRecarga) {
            iconoRecarga.classList.add('bi-arrow-repeat', 'spinner-border-sm');
        }
        try {
            const resp = await fetch(urlApiDetalle);
            const data = await resp.json();

            if (data.exito && data.datos && data.datos.actual) {
                renderizarFicha(data.datos.actual, data.datos.historial || []);
                if (cargando) cargando.style.display = 'none';
                if (contenido) contenido.style.display = 'block';
            } else {
                if (cargando) {
                    cargando.innerHTML = `
                        <div class="text-danger py-4">
                            <i class="bi bi-x-circle fs-1 mb-2 d-block"></i>
                            <h5>No se encontró el equipo</h5>
                            <p class="small text-muted">${data.mensaje || 'No hay diagnósticos disponibles con los parámetros solicitados.'}</p>
                            <a href="monitoreo_equipos.php" class="btn btn-outline-primary btn-sm mt-2">Volver al Monitoreo</a>
                        </div>
                    `;
                }
            }
        } catch (err) {
            if (cargando) {
                cargando.innerHTML = `
                    <div class="text-danger py-4">
                        <i class="bi bi-wifi-off fs-1 mb-2 d-block"></i>
                        <h5>Error de conexión con el backend</h5>
                        <p class="small text-muted">No se pudo obtener respuesta desde ${urlBackendBase}</p>
                        <button class="btn btn-primary btn-sm mt-2" onclick="location.reload()">Reintentar</button>
                    </div>
                `;
            }
        } finally {
            if (iconoRecarga) {
                iconoRecarga.classList.remove('bi-arrow-repeat', 'spinner-border-sm');
            }
        }
    }

    function renderizarFicha(d, historial) {
        // Cabecera
        const elBreadcrumb = document.getElementById('breadcrumbHostname');
        if (elBreadcrumb) elBreadcrumb.textContent = d.hostname || 'Equipo';

        const elHost = document.getElementById('txtHostname');
        if (elHost) elHost.textContent = d.hostname || '--';

        const elUser = document.getElementById('txtUsuario');
        if (elUser) elUser.textContent = d.usuario_windows || 'Sesión no identificada';

        const elIp = document.getElementById('txtIpLocal');
        if (elIp) elIp.textContent = d.ip_local || '--';

        const elMac = document.getElementById('txtMac');
        if (elMac) elMac.textContent = d.direccion_mac || '--';

        const elEvento = document.getElementById('txtTipoEvento');
        if (elEvento) elEvento.textContent = (d.tipo_evento || 'programado').toUpperCase();

        const placa = d.detalle_placa_bios || {};
        const elPlacaMod = document.getElementById('txtPlacaModelo');
        if (elPlacaMod) elPlacaMod.textContent = `${placa.fabricante || ''} ${placa.modelo || ''}`.trim() || 'Genérico';

        const elSerial = document.getElementById('txtSerial');
        if (elSerial) elSerial.textContent = placa.serial || d.numero_serie || 'N/A';

        // Badge global
        const badgeSalud = document.getElementById('badgeSaludGlobal');
        if (badgeSalud) {
            if (d.estado_salud_general === 'critico') {
                badgeSalud.className = 'badge bg-danger text-white rounded-pill px-3 py-1';
                badgeSalud.textContent = 'Crítico';
            } else if (d.estado_salud_general === 'advertencia') {
                badgeSalud.className = 'badge bg-warning text-dark rounded-pill px-3 py-1';
                badgeSalud.textContent = 'Advertencia';
            } else {
                badgeSalud.className = 'badge bg-success text-white rounded-pill px-3 py-1';
                badgeSalud.textContent = 'Salud Óptima';
            }
        }

        // Fechas
        const fReporte = formatearFecha(d.fecha_diagnostico);
        const elFechaRep = document.getElementById('txtFechaReporte');
        if (elFechaRep) elFechaRep.textContent = `${fReporte.fecha} ${fReporte.hora}`;

        const elFechaRel = document.getElementById('txtFechaRelativa');
        if (elFechaRel) elFechaRel.textContent = 'Hora del servidor UTC/Local';

        // Alertas banner
        const alertas = Array.isArray(d.alertas_detectadas) ? d.alertas_detectadas : [];
        const bannerAlerta = document.getElementById('alertaBanner');
        if (bannerAlerta) {
            if (alertas.length > 0) {
                bannerAlerta.classList.remove('d-none');
                const elAlertTxt = document.getElementById('alertaBannerTexto');
                if (elAlertTxt) elAlertTxt.textContent = `${alertas.length} advertencia(s) detectada(s): ${alertas[0]}`;
            } else {
                bannerAlerta.classList.add('d-none');
            }
        }

        // 1. CPU
        const cpu = d.detalle_cpu || {};
        const elCpuMod = document.getElementById('txtCpuModelo');
        if (elCpuMod) elCpuMod.textContent = cpu.modelo || 'Procesador Windows';

        const cpuCarga = parseFloat(d.uso_cpu_porcentaje) || 0;
        const elCpuVal = document.getElementById('txtCpuCargaValor');
        if (elCpuVal) elCpuVal.textContent = `${cpuCarga}%`;

        const progCpu = document.getElementById('progresoCpu');
        if (progCpu) {
            progCpu.style.width = `${Math.min(cpuCarga, 100)}%`;
            progCpu.className = `progress-bar ${cpu.estado === 'critico' ? 'bg-danger' : (cpu.estado === 'advertencia' ? 'bg-warning' : 'bg-primary')}`;
        }

        const elCpuNuc = document.getElementById('txtCpuNucleos');
        if (elCpuNuc) elCpuNuc.textContent = cpu.nucleos || '--';

        const elCpuHil = document.getElementById('txtCpuHilos');
        if (elCpuHil) elCpuHil.textContent = cpu.hilos || '--';

        const elCpuTmp = document.getElementById('txtCpuTemp');
        if (elCpuTmp) elCpuTmp.textContent = cpu.temperatura_c ? `${cpu.temperatura_c} °C` : 'N/A';

        const badgeCpu = document.getElementById('badgeEstadoCpu');
        if (badgeCpu) {
            badgeCpu.className = `badge ${cpu.estado === 'critico' ? 'bg-danger-subtle text-danger' : (cpu.estado === 'advertencia' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success')} border rounded-pill`;
            badgeCpu.textContent = (cpu.estado || 'optimo').toUpperCase();
        }

        const cajaAlertaCpu = document.getElementById('alertaCpuCaja');
        if (cajaAlertaCpu) {
            if (cpu.alerta) {
                cajaAlertaCpu.textContent = cpu.alerta;
                cajaAlertaCpu.classList.remove('d-none');
            } else {
                cajaAlertaCpu.classList.add('d-none');
            }
        }

        // 2. RAM
        const ram = d.detalle_ram || {};
        const ramUso = parseFloat(d.uso_ram_porcentaje) || 0;
        const elRamUso = document.getElementById('txtRamUsoValor');
        if (elRamUso) elRamUso.textContent = `${ramUso}%`;

        const progRam = document.getElementById('progresoRam');
        if (progRam) {
            progRam.style.width = `${Math.min(ramUso, 100)}%`;
            progRam.className = `progress-bar ${ram.estado === 'critico' ? 'bg-danger' : (ram.estado === 'advertencia' ? 'bg-warning' : 'bg-info')}`;
        }

        const elRamTot = document.getElementById('txtRamTotal');
        if (elRamTot) elRamTot.textContent = `${ram.total_gb || 0} GB`;

        const elRamUsd = document.getElementById('txtRamUsado');
        if (elRamUsd) elRamUsd.textContent = `${ram.usado_gb || 0} GB`;

        const elRamLib = document.getElementById('txtRamLibre');
        if (elRamLib) elRamLib.textContent = `${ram.libre_gb || 0} GB`;

        const elRamVel = document.getElementById('txtRamVelocidad');
        if (elRamVel) elRamVel.textContent = ram.velocidad_mhz ? `${ram.velocidad_mhz} MHz` : '--';

        const badgeRam = document.getElementById('badgeEstadoRam');
        if (badgeRam) {
            badgeRam.className = `badge ${ram.estado === 'critico' ? 'bg-danger-subtle text-danger' : (ram.estado === 'advertencia' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success')} border rounded-pill`;
            badgeRam.textContent = (ram.estado || 'optimo').toUpperCase();
        }

        const cajaAlertaRam = document.getElementById('alertaRamCaja');
        if (cajaAlertaRam) {
            if (ram.alerta) {
                cajaAlertaRam.textContent = ram.alerta;
                cajaAlertaRam.classList.remove('d-none');
            } else {
                cajaAlertaRam.classList.add('d-none');
            }
        }

        // 3. Discos
        const discos = Array.isArray(d.detalle_almacenamiento) ? d.detalle_almacenamiento : [];
        const contDiscos = document.getElementById('contenedorDiscos');
        if (contDiscos) {
            contDiscos.innerHTML = '';
            discos.forEach(disco => {
                const pctUsado = disco.tamano_gb > 0 ? Math.round(((disco.tamano_gb - disco.libre_gb) / disco.tamano_gb) * 100) : 0;
                let barraColor = 'bg-success';
                if (disco.estado === 'advertencia') barraColor = 'bg-warning';
                if (disco.estado === 'critico') barraColor = 'bg-danger';

                contDiscos.innerHTML += `
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold fs-5 text-dark">${disco.unidad}</span>
                                    <span class="badge ${disco.estado === 'critico' ? 'bg-danger text-white' : (disco.estado === 'advertencia' ? 'bg-warning text-dark' : 'bg-success text-white')} rounded-pill" style="font-size: 0.65rem;">
                                        ${(disco.estado || 'OK').toUpperCase()}
                                    </span>
                                </div>
                                <div class="text-secondary small mb-2 text-truncate" title="${disco.modelo || ''}">${disco.modelo || 'Disco Físico'}</div>
                                
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Libre: <strong>${disco.libre_gb} GB</strong></span>
                                    <span>Total: ${disco.tamano_gb} GB</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar ${barraColor}" style="width: ${pctUsado}%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between align-items-center small pt-2 border-top">
                                    <span class="text-muted" style="font-size: 0.72rem;">Salud SMART:</span>
                                    <span class="badge ${disco.smart_status === 'OK' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} border rounded-pill">
                                        ${disco.smart_status || 'OK'}
                                    </span>
                                </div>
                                ${disco.alerta ? `<div class="small text-danger fw-medium mt-2" style="font-size: 0.72rem;"><i class="bi bi-exclamation-triangle me-1"></i>${disco.alerta}</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        // Placa y Red
        const elPlacaF = document.getElementById('txtPlacaFab');
        if (elPlacaF) elPlacaF.textContent = placa.fabricante || 'N/A';

        const elPlacaM = document.getElementById('txtPlacaMod');
        if (elPlacaM) elPlacaM.textContent = placa.modelo || 'N/A';

        const elPlacaS = document.getElementById('txtPlacaSer');
        if (elPlacaS) elPlacaS.textContent = placa.serial || 'N/A';

        const red = d.detalle_red || {};
        const elRedNom = document.getElementById('txtRedNombre');
        if (elRedNom) elRedNom.textContent = red.adaptador || 'Adaptador genérico';

        const elRedGtw = document.getElementById('txtRedGateway');
        if (elRedGtw) elRedGtw.textContent = red.gateway || 'Sin puerta de enlace';

        const elRedDns = document.getElementById('txtRedDns');
        if (elRedDns) elRedDns.textContent = Array.isArray(red.dns) ? red.dns.join(', ') : (red.dns || '--');

        const badgeRed = document.getElementById('badgeEstadoRed');
        if (badgeRed) {
            badgeRed.className = `badge ${red.estado === 'advertencia' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success'} border rounded-pill`;
            badgeRed.textContent = (red.estado || 'optimo').toUpperCase();
        }

        // 4. Antivirus
        const av = d.seguridad_antivirus || {};
        const elAvNom = document.getElementById('txtAvNombre');
        if (elAvNom) elAvNom.textContent = av.nombre || 'Antivirus de Windows';

        const elAvFec = document.getElementById('txtAvFirmasFecha');
        if (elAvFec) elAvFec.textContent = av.ultima_definicion || 'Al día';

        const elAvAme = document.getElementById('txtAvAmenazas');
        if (elAvAme) elAvAme.textContent = `${av.amenazas_activas || 0} Amenazas detectadas`;

        const badgeAv = document.getElementById('badgeEstadoAv');
        if (badgeAv) {
            badgeAv.className = `badge ${av.estado === 'critico' ? 'bg-danger text-white' : (av.estado === 'advertencia' ? 'bg-warning text-dark' : 'bg-success text-white')} rounded-pill px-3 py-1`;
            badgeAv.textContent = (av.estado || 'optimo').toUpperCase();
        }

        // 5. Prefetch Apps
        const prefetch = Array.isArray(d.apps_recientes_prefetch) ? d.apps_recientes_prefetch : [];
        const tablaPf = document.getElementById('cuerpoTablaPrefetch');
        if (tablaPf) {
            if (prefetch.length > 0) {
                let pfHtml = '';
                prefetch.forEach(app => {
                    pfHtml += `
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark small"><i class="bi bi-app me-1 text-primary"></i>${app.ejecutable}</div>
                            </td>
                            <td class="small text-muted">${app.ultima_ejecucion || '--'}</td>
                            <td class="text-end"><span class="badge bg-light text-secondary border">${app.fuente || 'Prefetch'}</span></td>
                        </tr>
                    `;
                });
                tablaPf.innerHTML = pfHtml;
            } else {
                tablaPf.innerHTML = `<tr><td colspan="3" class="text-center py-3 text-muted small">Sin registros recientes de Prefetch.</td></tr>`;
            }
        }

        // 6. Descargas
        const descargas = Array.isArray(d.descargas_recientes) ? d.descargas_recientes : [];
        const listaDesc = document.getElementById('listaDescargas');
        if (listaDesc) {
            if (descargas.length > 0) {
                let descHtml = '';
                descargas.forEach(f => {
                    descHtml += `
                        <div class="list-group-item px-0 py-2 border-0 border-bottom d-flex justify-content-between align-items-center">
                            <div class="text-truncate me-2" style="max-width: 250px;">
                                <div class="small fw-semibold text-dark text-truncate" title="${f.archivo}">${f.archivo}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">${f.fecha || ''} &bull; ${f.tamano_kb} KB</div>
                            </div>
                            ${f.riesgo_potencial ? '<span class="badge bg-danger-subtle text-danger border rounded-pill" style="font-size: 0.65rem;">Riesgo .exe/.zip</span>' : '<span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Seguro</span>'}
                        </div>
                    `;
                });
                listaDesc.innerHTML = descHtml;
            } else {
                listaDesc.innerHTML = `<div class="text-muted small text-center py-3">No hay descargas registradas en los últimos días.</div>`;
            }
        }

        // 7. Alertas Log
        const logAlertas = document.getElementById('contenedorAlertasLog');
        if (logAlertas) {
            if (alertas.length > 0) {
                let aHtml = '';
                alertas.forEach(a => {
                    aHtml += `<div class="alert alert-warning py-2 px-3 small mb-2 d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i><span>${a}</span></div>`;
                });
                logAlertas.innerHTML = aHtml;
            } else {
                logAlertas.innerHTML = `<div class="alert alert-success py-2 px-3 small mb-0 d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill"></i><span>Todo en orden. No se detectaron fallos de hardware ni amenazas.</span></div>`;
            }
        }

        // 8. Historial
        const tablaHist = document.getElementById('cuerpoTablaHistorial');
        if (tablaHist) {
            if (historial.length > 0) {
                let histHtml = '';
                historial.forEach(h => {
                    const fH = formatearFecha(h.fecha_diagnostico);
                    let badgeH = '<span class="badge bg-success-subtle text-success">Óptimo</span>';
                    if (h.estado_salud_general === 'advertencia') badgeH = '<span class="badge bg-warning-subtle text-warning-emphasis">Advertencia</span>';
                    if (h.estado_salud_general === 'critico') badgeH = '<span class="badge bg-danger-subtle text-danger">Crítico</span>';

                    histHtml += `
                        <tr>
                            <td class="fw-bold">#${h.id}</td>
                            <td class="small">
                                <span class="text-dark fw-medium">${fH.fecha}</span>
                                <span class="text-muted ms-1">${fH.hora}</span>
                            </td>
                            <td><span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.65rem;">${h.tipo_evento}</span></td>
                            <td>${badgeH}</td>
                            <td class="small">${h.uso_cpu_porcentaje}%</td>
                            <td class="small">${h.uso_ram_porcentaje}%</td>
                            <td class="small">${h.espacio_libre_c_gb} GB</td>
                            <td><span class="badge ${h.salud_disco_smart === 'OK' ? 'bg-success-subtle text-success' : 'bg-danger text-white'}">${h.salud_disco_smart}</span></td>
                        </tr>
                    `;
                });
                tablaHist.innerHTML = histHtml;
            } else {
                tablaHist.innerHTML = `<tr><td colspan="8" class="text-center py-3 text-muted small">No hay historial previo registrado para este equipo.</td></tr>`;
            }
        }
    }

    function formatearFecha(isoString) {
        if (!isoString) return { fecha: '--', hora: '--' };
        try {
            const d = new Date(isoString);
            return {
                fecha: d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' }),
                hora: d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
            };
        } catch (e) {
            return { fecha: isoString, hora: '' };
        }
    }

    if (btnRecargar) {
        btnRecargar.addEventListener('click', cargarDetalle);
    }

    cargarDetalle();
});
