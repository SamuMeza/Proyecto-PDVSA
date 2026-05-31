# Tabla: `fotos_correctivas`

Fotografías adjuntas a una orden de trabajo correctiva.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `orden_correctiva_id` | INTEGER | FK → ordenes_correctivas.id, NOT NULL | Orden a la que pertenece |
| `ruta_archivo` | VARCHAR(255) | NOT NULL | Ruta física del archivo en servidor |
| `nombre_original` | VARCHAR(255) | NOT NULL | Nombre original del archivo subido |
| `tamano_kb` | INTEGER | NOT NULL | Tamaño en kilobytes después de compresión |
| `tipo` | VARCHAR(20) | DEFAULT 'durante' | Momento de la foto: antes / durante / despues |
| `subida_por_usuario_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que subió la foto |
| `fecha_subida` | TIMESTAMP | DEFAULT NOW() | Fecha de subida |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación del registro |

---

**Restricciones:**
- Máximo 3 fotos por orden correctiva (validado en aplicación)
- Compresión automática al subir

**Relaciones:**
- Muchos a uno con `ordenes_correctivas`
- Muchos a uno con `usuarios`

**Índices:**
- `idx_fotos_otc` (orden_correctiva_id)
- `idx_fotos_tipo` (tipo)
