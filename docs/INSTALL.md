# Guía de Instalación – Sistema CMMS PDVSA Punta de Mata

## Requisitos

- PHP 8.1+
- PostgreSQL 14+ (o MySQL 8+)
- Apache/Nginx
- Composer

## Pasos

1. Clonar el repositorio:
   ```bash
   git clone <url> /var/www/sistema_pdvsa
   ```

2. Copiar y configurar variables de entorno:
   ```bash
   cp .env.example .env
   nano .env
   ```

3. Instalar dependencias PHP:
   ```bash
   composer install
   ```

4. Crear la base de datos:
   ```bash
   # PostgreSQL
   createdb sistema_pdvsa
   psql -d sistema_pdvsa < database/migrations/001_create_localidades.sql
   # ... ejecutar migraciones 001-024 en orden

   # MySQL
   mysql -u root < database/migrations/001_create_localidades.sql
   # ... ejecutar migraciones 001-024 en orden
   ```

5. Sembrar datos iniciales:
   ```bash
   # PostgreSQL
   psql -d sistema_pdvsa < database/seeds/001_seed_localidades.sql
   # ... ejecutar seeds 001-008 en orden

   # MySQL
   mysql -u root < database/seeds/001_seed_localidades.sql
   # ... ejecutar seeds 001-008 en orden
   ```

6. Configurar Apache:
   ```apache
   DocumentRoot /var/www/sistema_pdvsa/public
   <Directory /var/www/sistema_pdvsa/public>
       AllowOverride All
       Require all granted
   </Directory>
   ```

7. Acceder:
   - URL: `http://localhost/sistema_pdvsa`
   - Admin: `admin` / `Admin2026!`

## Verificación

- Login con admin/Admin2026!
- Dashboard funcional
- Módulo de equipos operativo
