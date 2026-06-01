# Tabla: `niveles_mantenimiento`

Configuración de niveles de mantenimiento preventivo por categoría de equipo.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `categoria_id` | INTEGER | FK → categorias_equipo.id, NOT NULL | Categoría a la que aplica |
| `nombre_nivel` | VARCHAR(50) | NOT NULL | Nombre del nivel (Nivel 1, Nivel 2, etc.) |
| `frecuencia` | VARCHAR(50) | NOT NULL | Frecuencia: Semanal, Quincenal, Mensual, Trimestral, Anual |
| `duracion_estimada_horas` | DECIMAL(4,2) | NOT NULL | Duración estimada en horas |
| `cantidad_ejecutores_requeridos` | INTEGER | DEFAULT 1 | Cantidad de personas requeridas |
| `descripcion` | TEXT | NULL | Descripción del nivel de mantenimiento |
| `es_automatico` | BOOLEAN | DEFAULT TRUE | Si se genera automáticamente en el calendario |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Niveles confirmados:**

**PLC:**
- Nivel 1: Mensual, 1 hora, 1 persona
- Nivel 2: Trimestral, 2 horas, 1 persona
- Nivel 3: Anual, 8 horas, 1 persona

**SCADA:**
- Nivel 1: Semanal, 0.25 horas, 1 persona
- Nivel 2: Quincenal, 1 hora, 1 persona
- Nivel 3: Mensual, 2 horas, 1 persona

**Niveles 4-5 (fuera de plan, manuales):**
- Nivel 4: Prueba de lazo (correctivo)
- Nivel 5: Migración / Mantenimiento Mayor

**Relaciones:**
- Muchos a uno con `categorias_equipo`
- Uno a muchos con `checklists`
- Uno a muchos con `ordenes_preventivas`

**Índices:**
- `idx_niveles_categoria` (categoria_id)
- `idx_niveles_frecuencia` (frecuencia)
