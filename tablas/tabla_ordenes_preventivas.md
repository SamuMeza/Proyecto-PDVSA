# Tabla: `ordenes_preventivas`

Órdenes de trabajo de mantenimiento preventivo.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `codigo_unico` | VARCHAR(50) | NOT NULL, UNIQUE | Código único (ej: OTP-2026-0001) |
| `equipo_id` | INTEGER | FK → equipos.id, NOT NULL | Equipo al que aplica |
| `nivel_mantenimiento_id` | INTEGER | FK → niveles_mantenimiento.id, NOT NULL | Nivel de mantenimiento |
| `fecha_planificada` | DATE | NOT NULL | Fecha planificada para el mantenimiento |
| `fecha_asignacion` | DATE | NULL | Fecha en que se asignó al mantenedor |
| `fecha_inicio_ejecucion` | TIMESTAMP | NULL | Fecha/hora real de inicio |
| `fecha_cierre_ejecucion` | TIMESTAMP | NULL | Fecha/hora real de cierre |
| `estado` | VARCHAR(50) | DEFAULT 'planificada' | Estado de la orden |
| `planificador_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que generó la orden |
| `supervisor_asigno_id` | INTEGER | FK → usuarios.id, NULL | Supervisor que asignó al técnico |
| `mantenedor_id` | INTEGER | FK → usuarios.id, NULL | Técnico asignado para ejecutar |
| `duracion_estimada_horas` | DECIMAL(4,2) | NOT NULL | Copia del nivel (duración estimada) |
| `duracion_real_horas` | DECIMAL(4,2) | NULL | Duración real calculada al cierre |
| `observaciones_mantenedor` | TEXT | NULL | Observaciones del técnico al ejecutar |
| `observaciones_supervisor` | TEXT | NULL | Observaciones del supervisor post-cierre |
| `motivo_suspension` | TEXT | NULL | Motivo si la orden fue suspendida |
| `generada_automaticamente` | BOOLEAN | DEFAULT TRUE | Si fue generada por el sistema o manualmente |
| `creada_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación de la orden |
| `actualizada_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Estados posibles:**
- `planificada` – Generada por el sistema o manualmente, sin asignar
- `programada` – Confirmada en calendario, pendiente de asignación
- `asignada` – Asignada a un mantenedor específico
- `en_ejecucion` – El mantenedor inició el trabajo
- `ejecutada` – El mantenedor marcó como completada
- `cerrada` – Supervisor auditó y cerró oficialmente
- `vencida` – No se ejecutó en la fecha planificada
- `suspendida` – Equipo inactivo o impedimento operativo

**Relaciones:**
- Muchos a uno con `equipos`
- Muchos a uno con `niveles_mantenimiento`
- Muchos a uno con `usuarios` (planificador, supervisor, mantenedor)
- Uno a muchos con `ejecuciones_preventivas`

**Índices:**
- `idx_otp_codigo` (codigo_unico)
- `idx_otp_equipo` (equipo_id)
- `idx_otp_estado` (estado)
- `idx_otp_fecha_planificada` (fecha_planificada)
- `idx_otp_mantenedor` (mantenedor_id)
