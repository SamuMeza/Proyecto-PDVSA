## Context

El proyecto actualmente cuenta con scripts de bases de datos (`sql/schema_mysql.sql` y `sql/schema_postgresql.sql`) que definen solo las tablas de roles y usuarios. Se han modelado 24 tablas en total en la carpeta `tablas/`, que abarcan las entidades necesarias para el sistema de mantenimiento (áreas, instalaciones, equipos, calendarios, órdenes preventivas/correctivas, catálogos, configuraciones, etc.).

## Goals / Non-Goals

**Goals:**
- Traducir las definiciones de las 24 tablas en formato markdown a sentencias DDL (Data Definition Language) estándar para MySQL y PostgreSQL.
- Establecer las llaves primarias, foráneas, índices, restricciones UNIQUE, y valores por defecto.
- Asegurar la compatibilidad con el motor de base de datos actual utilizado por PDO.

**Non-Goals:**
- Modificar el código PHP del sistema; este cambio es puramente de infraestructura de base de datos.
- Poblar la base de datos con datos de prueba, a excepción de los catálogos estáticos iniciales básicos definidos en las tablas (ej: estados por defecto, configuraciones de sistema).

## Decisions

- **Unificación de DDL**: Se actualizarán directamente los archivos `schema_mysql.sql` y `schema_postgresql.sql`.
- **Nomenclatura**: Se mantendrán los nombres de tablas y columnas exactamente como están definidos en los archivos markdown de la carpeta `tablas/` para asegurar la coherencia (snake_case).
- **Tipos de Datos**:
  - Para IDs auto-incrementables se usará `SERIAL` en PostgreSQL y `INT AUTO_INCREMENT` en MySQL.
  - Para campos booleanos se usarán tipos nativos (`BOOLEAN` o `TINYINT(1)` según corresponda).
  - Para campos JSON se usará `JSONB` en PostgreSQL y `JSON` en MySQL.

## Risks / Trade-offs

- **[Riesgo]** Conflictos en ejecución repetida (scripts no idempotentes) → **Mitigación**: Se utilizará `IF NOT EXISTS` en todas las creaciones de tablas e índices para asegurar que los scripts se puedan ejecutar múltiples veces sin error.
- **[Riesgo]** Errores de dependencias en llaves foráneas → **Mitigación**: Se ordenarán las sentencias `CREATE TABLE` en los scripts SQL respetando el orden topológico de las dependencias.
