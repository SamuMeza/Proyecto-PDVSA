# Roles y Permisos

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| Administrador | Gestión total | Todo el sistema |
| Planificador/Programador | Configura frecuencias, genera calendario | Equipos, Preventivo, Reportes |
| Supervisor | Asigna, audita, desbloquea | Preventivo, Correctivo, Reportes |
| Mantenedor | Ejecuta, cierra, reporta | Preventivo, Correctivo |

## Permisos por Módulo

Los permisos se gestionan mediante `permisos_json` en la tabla `roles`.
Estructura:
```json
{
    "equipos": { "ver": true, "crear": true, "editar": false, "eliminar": false },
    "preventivas": { "ver": true, "ejecutar": true, "cerrar": true },
    "correctivas": { "ver": true, "reportar": true, "cerrar": false }
}
```
