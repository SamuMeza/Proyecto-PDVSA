# Sistema PDVSA

## Estructura

```
sistema_pdvsa/
├── index.php              → Redirige a /public
├── config/db.php          → Conexión PDO (MySQL o PostgreSQL)
├── auth/                  → Login, registro, logout
├── css/                   → Estilos y scripts de tema/sidebar
├── public/                → Páginas principales (requieren sesión)
├── sql/                   → Esquemas MySQL y PostgreSQL
└── scripts/               → Migración MySQL → PostgreSQL
```

## Instalación (XAMPP / MySQL)

1. Iniciar Apache y MySQL en XAMPP.
2. Importar el esquema:
   ```bash
   mysql -u root < sql/schema_mysql.sql
   ```
   O desde phpMyAdmin: importar `sql/schema_mysql.sql`.
3. Ajustar credenciales en `config/db.php` si es necesario (por defecto: `root` sin contraseña).
4. Abrir: `http://localhost/sistema_pdvsa/`

## Autenticación

- **Login:** `/sistema_pdvsa/auth/login.php`
- **Usuario administrador por defecto:**
  - Usuario: `admin`
  - Contraseña: `Admin2026!`
- **Registro de usuarios:** solo accesible con sesión de rol **Administrador** (`/auth/register.php` o menú «Registrar usuario»).
- Las páginas en `public/` requieren sesión activa.

Si la base ya existía sin el admin, ejecutar:
```bash
mysql -u root sistema_pdvsa < sql/seed_admin.sql
```

## Tema y navegación

- Barra lateral izquierda con botón para ocultar/mostrar.
- Modo claro/oscuro con paleta: `#7A7A7A`, `#870707`, `#EDEDED`, `#212121`.

## PostgreSQL

1. Crear base y tablas con `sql/schema_postgresql.sql`.
2. En `config/db.php` o variables de entorno, usar `DB_DRIVER=pgsql`.

## Migración MySQL → PostgreSQL

```bash
php scripts/migrate_mysql_to_postgresql.php
```

Variables opcionales: `MYSQL_HOST`, `MYSQL_DB`, `PG_HOST`, `PG_DB`, etc.
