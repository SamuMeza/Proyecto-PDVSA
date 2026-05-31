# Configuración del Servidor

## Estructura de Archivos de Configuración

| Archivo | Propósito |
|---------|-----------|
| `.env` | Credenciales BD, claves |
| `config/database.php` | Conexión PostgreSQL/MySQL |
| `config/app.php` | Timezone, locale, nombre app |
| `config/session.php` | Timeouts, cookies |
| `config/routes.php` | Definición de rutas |

## Configuración Recomendada de PHP

```ini
memory_limit = 256M
upload_max_filesize = 32M
post_max_size = 32M
max_execution_time = 120
date.timezone = America/Caracas
```

## Apache

Habilitar `mod_rewrite`:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Nginx

```nginx
server {
    listen 80;
    server_name sistema-pdvsa.local;
    root /var/www/sistema_pdvsa/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
