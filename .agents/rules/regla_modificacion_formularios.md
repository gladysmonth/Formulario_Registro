# Regla de Modificación Modular por Secciones en Formularios

A partir de este momento, cada vez que vayas a modificar o estructurar código en el archivo `formulario_soporte.php` o en cualquier vista de formulario, **debes seguir estrictamente la regla de trabajar por partes mediante secciones bien delimitadas**.

## Instrucciones paso a paso que debes cumplir:

1. **No generes código monolítico ni archivos gigantes:** Prohibido crear bloques masivos e interminables de código de una sola vez.
2. **Respeta la modularidad por secciones:** El formulario debe mantenerse dividido lógicamente en sus secciones ordenadas (como Datos Generales, Tipo de Soporte, Detalle, Prioridad, Datos del Técnico y Observaciones).
3. **Modificaciones quirúrgicas:** Cuando te pida un cambio, edita **únicamente** la sección o el bloque específico solicitado, sin tocar, reescribir ni alterar el resto del archivo que ya está funcionando.
4. **Protege el entorno:** Mantén siempre la compatibilidad estricta con PHP 7.3, la estructura de Bootstrap 5 y la arquitectura definida en el proyecto.
5. **Si un componente es muy extenso:** Si la lógica de una sección o bloque requiere crecer demasiado, propón aislarlo de forma limpia pero mantén la separación estricta para evitar archivos robustos y facilitar futuros cambios.
