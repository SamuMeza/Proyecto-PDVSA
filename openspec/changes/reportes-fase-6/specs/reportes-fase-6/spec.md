# Especificación - Reportes Fase 6

## Descripción

Entrega reportes de órdenes y equipos con filtros avanzados, indicadores clave y exportación de resultados para análisis operativo.

## Requisitos

- Debe existir un reporte de lista de órdenes con filtros por `estado`, `tipo`, `familia` y rango de fechas.
- Debe mostrar indicadores de disponibilidad de equipos y cumplimiento de órdenes.
- Debe permitir exportar los reportes más importantes a PDF y CSV.
- El acceso debe estar limitado a usuarios con permiso `ver_reportes`.
- Debe documentarse el uso de endpoints y plantillas en `docs/`.

## Criterios de aceptación

- El reporte muestra datos filtrados correctamente según los criterios ingresados.
- Los indicadores se calculan y muestran en forma de números clave o tarjetas resumidas.
- La exportación a PDF y CSV descarga archivos válidos con el contenido filtrado.
- Usuarios sin permiso `ver_reportes` no pueden acceder a los reportes.
- La documentación en `docs/` describe los endpoints y plantillas usados.

## Observaciones

- Este módulo puede usar datos de órdenes preventivas y correctivas para los indicadores.
- La exportación debe respetar el mismo conjunto de filtros aplicado en la vista.
