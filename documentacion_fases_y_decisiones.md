# Decisiones Consolidadas y Fases del Proyecto

Este documento recopila la información, decisiones y estructura confirmadas para el desarrollo del Sistema de Mantenimiento PDVSA, abarcando las 6 fases de implementación y la matriz de permisos.

## Decisiones Generales Confirmadas

- **Base de Datos**: Se trabajará con 24 tablas (2 principales de usuarios/roles y 22 adicionales para la infraestructura de mantenimiento).
- **Control de Permisos**: Uso del campo `permisos_json` estructurado como `{"modulo": {"accion": true}}`.
- **Límite OTP**: Se permite un máximo de 150 intentos de OTP por usuario al día, validado a nivel de hora/día.
- **Categorías**: Se confirman las 13 categorías, incluyendo "Servicios Auxiliares", "Infraestructura" y "Automatización".
- **Identidad Visual**: Uso del logotipo rojo con X geométrica y una interfaz premium con soporte para modo oscuro.

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
