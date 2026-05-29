## Why

Actualmente, solo existe un esquema de base de datos básico con dos tablas (roles y usuarios). Para dar soporte completo a todas las funcionalidades del Sistema de Mantenimiento PDVSA (gestión de equipos, órdenes preventivas/correctivas, catálogos y auditorías), es necesario implementar el esquema de base de datos completo.

## What Changes

- **Implementación del Esquema Completo**: Se añadirán las 22 tablas restantes (áreas, localidades, equipos, órdenes preventivas, órdenes correctivas, etc.) a los scripts SQL existentes.
- **Relaciones y Restricciones**: Definición de todas las llaves foráneas y restricciones necesarias para mantener la integridad referencial.

## Capabilities

### New Capabilities
- `db-schema-full`: Implementación completa de las 24 tablas del sistema con sus relaciones.

### Modified Capabilities

## Impact

- El impacto principal es en los scripts SQL de inicialización (`schema_mysql.sql` y `schema_postgresql.sql`).
- La base de datos estará preparada para soportar el desarrollo de todos los módulos del sistema.
