## ADDED Requirements

### Requirement: Mapeo y Verificación de Permisos Granulares
El sistema SHALL leer el campo `permisos_json` de la tabla de roles correspondiente al usuario autenticado y verificar si tiene permiso para realizar una acción específica en un módulo dado.

#### Scenario: Usuario con permiso concedido
- **WHEN** un usuario con rol de Supervisor intenta acceder a una acción para la cual su campo `permisos_json` contiene `{"bloques": {"crear": true}}`
- **THEN** el sistema debe autorizar el acceso y permitir la ejecución de la acción.

#### Scenario: Usuario sin permiso denegado
- **WHEN** un usuario con rol de Planificador intenta realizar una acción para la cual su campo `permisos_json` no tiene el permiso concedido
- **THEN** el sistema debe denegar el acceso, redirigir a una página de error o mostrar un mensaje de falta de permisos.
