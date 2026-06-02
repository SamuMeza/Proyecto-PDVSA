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

## Patrón de Servicios

Todos los servicios del sistema (`AuthService`, `EquipoService`, `PreventivaService`, `CorrectivaService`, `CalendarioService`, `ReporteService`) siguen el patrón de **métodos estáticos**.

### Ventajas
- Simple de usar: `AuthService::login($user, $pass)`
- No requiere inyección de dependencias
- Fácil de testear con mocking estático

### Limitaciones
- No permite inyección de dependencias (DI)
- Difícil de mockear en tests unitarios
- Acoplamiento estático entre clases

### Decisión de diseño
Se mantiene el patrón estático por simplicidad. Para la próxima iteración mayor, se considerará migrar a servicios con DI usando un contenedor de dependencias.
