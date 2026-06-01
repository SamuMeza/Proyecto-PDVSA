# Tabla: `ejecuciones_preventivas`

Registro de la ejecución real de una orden preventiva por un mantenedor.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `orden_preventiva_id` | INTEGER | FK → ordenes_preventivas.id, NOT NULL | Orden que se ejecutó |
| `mantenedor_id` | INTEGER | FK → usuarios.id, NOT NULL | Técnico que ejecutó |
| `fecha_inicio_real` | TIMESTAMP | NULL | Fecha/hora real de inicio de ejecución |
| `fecha_fin_real` | TIMESTAMP | NULL | Fecha/hora real de fin de ejecución |
| `observaciones_mantenedor` | TEXT | NULL | Observaciones del técnico |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `ordenes_preventivas`
- Muchos a uno con `usuarios` (mantenedor)
- Uno a muchos con `ejecucion_checklist_items`

**Índices:**
- `idx_ejecuciones_otp` (orden_preventiva_id)
- `idx_ejecuciones_mantenedor` (mantenedor_id)
