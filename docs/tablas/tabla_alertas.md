# Tabla: `alertas`

Alertas y notificaciones internas del sistema (sin email).

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `usuario_destino_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que recibe la alerta |
| `tipo_alerta` | VARCHAR(50) | NOT NULL | Tipo: mantenimiento_proximo / equipo_vencido / equipo_inactivo_suspendido / tarea_asignada / calibracion_proxima |
| `mensaje` | TEXT | NOT NULL | Texto descriptivo de la alerta |
| `entidad_relacionada_tipo` | VARCHAR(20) | NULL | Tipo de entidad: preventiva / correctiva / equipo / calibracion |
| `entidad_relacionada_id` | INTEGER | NULL | ID de la entidad relacionada |
| `fecha_generacion` | TIMESTAMP | DEFAULT NOW() | Fecha en que se generó la alerta |
| `fecha_vencimiento_referencia` | DATE | NULL | Fecha límite a la que se refiere la alerta |
| `leida` | BOOLEAN | DEFAULT FALSE | Si el usuario ya la leyó |
| `fecha_lectura` | TIMESTAMP | NULL | Fecha en que fue leída |
| `accion_tomada` | VARCHAR(20) | NULL | Acción del usuario: ver_ahora / recordar_despues / ignorar |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |

---

**Relaciones:**
- Muchos a uno con `usuarios` (destinatario)

**Índices:**
- `idx_alertas_usuario` (usuario_destino_id)
- `idx_alertas_tipo` (tipo_alerta)
- `idx_alertas_leida` (leida)
- `idx_alertas_fecha` (fecha_generacion)
