## ADDED Requirements

### Requirement: Esquema de Tablas Base
El sistema SHALL inicializar la base de datos con todas las tablas maestras, transaccionales y de configuración, de acuerdo con los modelos definidos en la carpeta `docs/tablas/`.

#### Scenario: Creación inicial del esquema
- **WHEN** un administrador ejecuta los scripts de creación (`schema_mysql.sql` o `schema_postgresql.sql`) en una base de datos vacía
- **THEN** la base de datos debe crear las 24 tablas correspondientes sin errores, estableciendo las llaves primarias, llaves foráneas e índices.

### Requirement: Integridad Referencial
El sistema SHALL mantener la integridad referencial en todas las tablas mediante el uso de llaves foráneas (`FOREIGN KEY`) según lo estipulado en el modelado.

#### Scenario: Eliminación en cascada o restricción
- **WHEN** se intenta eliminar un registro que es referenciado por otra tabla como llave foránea
- **THEN** la base de datos debe rechazar la eliminación o aplicar la cascada, según la configuración de la llave, para evitar registros huérfanos.
