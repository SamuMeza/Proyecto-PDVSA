# Tabla: `equipos`

Maestro de activos – Registro completo de todos los equipos de la planta.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | ID único interno del sistema |
| `numero_activo_fijo` | VARCHAR(100) | NOT NULL, UNIQUE | Número de activo fijo interno de PDVSA |
| `serial` | VARCHAR(100) | NULL | Número de serie del fabricante |
| `nombre` | VARCHAR(200) | NOT NULL | Nombre descriptivo del equipo |
| `marca` | VARCHAR(100) | NULL | Marca del equipo |
| `modelo` | VARCHAR(100) | NULL | Modelo del equipo |
| `descripcion` | TEXT | NULL | Descripción técnica del equipo |
| `categoria_id` | INTEGER | FK → categorias_equipo.id, NOT NULL | Categoría del equipo |
| `zona_id` | INTEGER | FK → zonas.id, NOT NULL | Ubicación física del equipo |
| `grupo_responsable` | VARCHAR(100) | NULL | Grupo o departamento responsable |
| `grupo_seguridad_id` | INTEGER | FK → grupos_seguridad.id, NULL | Grupo de seguridad al que pertenece |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado operativo: activo / inactivo |
| `foto_equipo` | VARCHAR(255) | NULL | Ruta a foto principal del equipo |
| `fecha_registro` | DATE | DEFAULT NOW() | Fecha de registro en el sistema |
| `registrado_por_usuario_id` | INTEGER | FK → usuarios.id, NOT NULL | Usuario que registró el equipo |
| `ultima_modificacion` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |
| `modificado_por_usuario_id` | INTEGER | FK → usuarios.id, NULL | Usuario que modificó por última vez |
| `esta_bloqueado` | BOOLEAN | DEFAULT FALSE | Indica si el equipo está bloqueado por mantenimiento vencido |
| `motivo_bloqueo` | TEXT | NULL | Justificación del bloqueo (si aplica) |
| `bloqueado_por_usuario_id` | INTEGER | FK → usuarios.id, NULL | Usuario que bloqueó/desbloqueó |
| `fecha_bloqueo` | TIMESTAMP | NULL | Fecha del bloqueo |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación del registro |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última actualización |

---

**Relaciones:**
- Muchos a uno con `categorias_equipo`
- Muchos a uno con `zonas`
- Muchos a uno con `grupos_seguridad`
- Muchos a uno con `usuarios` (registrado_por, modificado_por, bloqueado_por)
- Uno a muchos con `ordenes_preventivas`
- Uno a muchos con `ordenes_correctivas`
- Uno a muchos con `calibraciones`

**Índices:**
- `idx_equipos_numero_activo` (numero_activo_fijo)
- `idx_equipos_nombre` (nombre)
- `idx_equipos_categoria` (categoria_id)
- `idx_equipos_zona` (zona_id)
- `idx_equipos_estado` (estado)
- `idx_equipos_bloqueado` (esta_bloqueado)
