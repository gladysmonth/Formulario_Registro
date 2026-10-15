const labels = ['Soporte Tecn.', 'Incidencias', 'Accesos', 'Mant.Preventivo'];
const counts = [9, 5, 5, 4];

labels.forEach((lbl, idx) => {
    const context = { label: lbl, raw: counts[idx] };
    const valor = context.raw || 0;
    const tooltipText = `${context.label}: ${valor}`;
    console.log(tooltipText);
});
