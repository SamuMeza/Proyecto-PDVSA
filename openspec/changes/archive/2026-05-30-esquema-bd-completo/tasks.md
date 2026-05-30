## 1. Actualización de script PostgreSQL

- [x] 1.1 Traducir las definiciones markdown de las 22 tablas faltantes a sentencias `CREATE TABLE IF NOT EXISTS` en PostgreSQL.
- [x] 1.2 Agregar las sentencias en `sql/schema_postgresql.sql` respetando el orden topológico (tablas independientes primero, luego las dependientes).
- [x] 1.3 Agregar los `CREATE INDEX IF NOT EXISTS` correspondientes a PostgreSQL.

## 2. Actualización de script MySQL

- [x] 2.1 Traducir las definiciones markdown de las 22 tablas faltantes a sentencias `CREATE TABLE IF NOT EXISTS` en MySQL.
- [x] 2.2 Agregar las sentencias en `sql/schema_mysql.sql` respetando el orden topológico.
- [x] 2.3 Agregar las configuraciones de motor (`ENGINE=InnoDB`) y charset (`DEFAULT CHARSET=utf8mb4`).

## 3. Pruebas de Sintaxis y Ejecución

- [x] 3.1 Probar la ejecución de `sql/schema_postgresql.sql` contra una base de datos PostgreSQL local en limpio para confirmar que no haya errores de sintaxis o dependencias circulares.
- [x] 3.2 Probar la ejecución de `sql/schema_mysql.sql` contra una base de datos MySQL local en limpio para validar las llaves foráneas.
