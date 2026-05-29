o# Decisiones Consolidadas y Fases del Proyecto

Este documento recopila la información, decisiones y estructura confirmadas para el desarrollo del Sistema de Mantenimiento PDVSA, abarcando las 6 fases de implementación y la matriz de permisos.

## Decisiones Generales Confirmadas

- **Base de Datos**: Se trabajará con 24 tablas (2 principales de usuarios/roles y 22 adicionales para la infraestructura de mantenimiento).
- **Control de Permisos**: Uso del campo `permisos_json` estructurado como `{"modulo": {"accion": true}}`.
- **Registro de usuarios**: Solo el rol Administrador puede crear cuentas.
- **Email interno**: El correo se genera como `nombre_usuario@pdvsa.com` y se guarda en la base de datos.
- **Teléfono**: Se guarda en un solo campo y obliga al prefijo `+58`.
- **Sesión inactiva**: Admin 10 min, Supervisor 20 min, otros 35 min; modal 2 min antes, logout inmediato si se ignora.
- **UI tema global**: El modo claro/oscuro aplica a todo el sistema, incluyendo mensajes, formularios y tablas.
- **Familia de equipos**: Se agrega `familia` en `categorias_equipo`; los valores pueden ser de prueba mientras se define el catálogo.
- **Bloques preventivos**: `ordenes_preventivas` usa `hora_inicio` y `hora_fin` para el bloque de mantenimiento.
- **Usuario único**: `nombre_usuario` es único con longitud hasta 150 caracteres.
- **Filtros de equipos**: La lista debe permitir filtrar por familia, categoría, zona y estado.
- **Equipos inactivos**: Se muestran en listados con indicación de estado `inactivo`.
- **Visibilidad de equipos**: Solo Admin, Supervisor y Programador pueden ver el listado de equipos.
- **Planificador**: Puede editar bloques existentes y también crear nuevas OT/solicitudes de planificación.
- **Calendario**: Debe mostrar vistas semanal y mensual, y permitir arrastrar/editar bloques.
- **Colores de familia**: Configurables desde `configuracion_sistema`.
- **CRUD OTC**: Admin puede todo; Supervisor y Programador pueden leer y actualizar; Mantenedor solo leer.
- **Catálogos iniciales**: `tipos_falla`, `prioridades_falla`, `categorias_equipo`, `zonas`.
- **Fotos**: Máximo 3 fotos, compresión, formato JPG/PNG.
- **Auditoría**: Registrar creación, edición, cierre y cambios de estado.
- **Reportes fase 6**: Primera versión guarda filtros y evento de generación; el PDF real queda para después.
- **Reporte UI**: El botón "Generar reporte" puede mostrar un estado "En desarrollo" y aún así registrar la petición.

## Matriz de Roles y Permisos Clave

| Permiso / Acción | Crear bloque | Editar bloque | Ver calendario | Crear OTP | Editar OTP | Admin usuarios |
|------------------|:------------:|:-------------:|:--------------:|:---------:|:----------:|:--------------:|
| Administrador    | ✓            | ✓             | ✓              | ✓         | ✓          | ✓              |
| Supervisor       | ✓            | ✓             | ✓              | ✓         | ✗          | ✗              |
| Planificador     | ✗            | ✗             | ✓              | ✓         | ✓          | ✗              |
| Mantenedor       | ✗            | ✗             | ✗              | ✓         | ✗          | ✗              |

*(Nota: Esta matriz refleja las decisiones acordadas durante la fase de exploración inicial).*

## Fases del Proyecto

1. **Fase 1: mejoras-auth-ui-infra**
   - Autenticación en dos pasos (OTP).
   - Control de límite de generación OTP (150 diarios por usuario).
   - Control de permisos mediante `permisos_json`.
   - Layout y tema UI premium (claro/oscuro).

2. **Fase 2: esquema-bd-completo**
   - Definición de las 24 tablas en SQL.
   - Restricciones, índices y llaves foráneas.

3. **Fase 3: Módulos Base y Catálogos**
   - (Pendiente de propuesta openspec)
   - Gestión de áreas, zonas, localidades.
   - Categorías y familias de equipos.

4. **Fase 4: Calendario y Planificación**
   - (Pendiente de propuesta openspec)
   - Vistas por semana/mes, filtros por familia.
   - Lógica de asignación de bloques.

5. **Fase 5: Mantenimiento Preventivo y Órdenes**
   - (Pendiente de propuesta openspec)
   - Generación de órdenes de trabajo (OT).
   - Asignación de técnicos.

6. **Fase 6: Ejecución, Reportes y Auditoría**
   - (Pendiente de propuesta openspec)
   - Checklists, captura de fotos.
   - Generación de reportes PDF.
