# Tabla: `configuracion_sistema`

Parámetros configurables del sistema.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `clave` | VARCHAR(100) | NOT NULL, UNIQUE | Nombre interno del parámetro |
| `valor` | TEXT | NOT NULL | Valor del parámetro |
| `descripcion` | TEXT | NULL | Descripción legible para el admin |
| `modificable_por` | VARCHAR(50) | DEFAULT 'admin' | Rol que puede modificar: admin / supervisor / planificador |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Parámetros iniciales:**

| Clave | Valor por defecto | Descripción |
|-------|-------------------|-------------|
| `tiempo_inactividad_minutos` | 60 | Minutos antes de cerrar sesión automáticamente |
| `dias_alerta_previa` | 7 | Días de antelación para alertas de mantenimiento |
| `tolerancia_minutos` | 15 | Tolerancia para ajuste de hora por mantenedor |
| `ruta_logo_pdvsa` | /assets/logo.png | Ruta al logo institucional |
| `texto_pie_pagina` | © 2026 PDVSA... | Texto del pie de página |
| `email_administrador` | admin@pdvsa.local | Email para recuperación de contraseña |
| `max_fotos_por_orden` | 3 | Máximo de fotos permitidas |
| `tamano_max_foto_kb` | 2048 | Tamaño máximo de foto antes de compresión |
| `sesion_expiracion_horas` | 1 | Horas de validez del token de sesión |

**Índices:**
- `idx_config_clave` (clave)
