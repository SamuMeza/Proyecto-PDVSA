# Tabla: `categorias_equipo`

Clasificación técnica de los activos industriales.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `nombre` | VARCHAR(100) | NOT NULL, UNIQUE | Nombre de la categoría |
| `descripcion` | TEXT | NULL | Descripción de la categoría |
| `color_calendario` | VARCHAR(7) | DEFAULT '#7BA7D9' | Color HEX para leyenda del calendario |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado del registro |
| `creado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Categorías confirmadas:**
1. DCS (Sistema de Control Distribuido)
2. SCADA (Sistema de Supervisión, Control y Adquisición de Datos)
3. ESD (Sistema de Parada de Emergencia)
4. PLC (Controladores Lógicos Programables)
5. Instrumentación Especializada
6. Banco de Baterías
7. UPS (Sistema de Alimentación Ininterrumpida)
8. Rectificadores
9. Motogenerador
10. Planta Física
11. Servicios Auxiliares [¿NUEVO?]
12. Infraestructura [¿NUEVO?]
13. Automatización [¿NUEVO?]

**Relaciones:**
- Uno a muchos con `equipos`
- Uno a muchos con `niveles_mantenimiento`

**Índices:**
- `idx_categorias_nombre` (nombre)
