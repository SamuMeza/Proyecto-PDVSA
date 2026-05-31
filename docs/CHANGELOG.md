# Changelog

## [1.0.0] - 2026-05-30

### Añadido
- Migración progresiva a MVC (src/ completa con Core, Models, Controllers, Services, Views, Middleware, Helpers, Exceptions)
- 57 archivos PHP en src/ (~3000 líneas)
- Front controller con enrutamiento (config/routes.php)
- Sistema OTP de 2 factores
- Sistema de alertas
- Generación de calendario automático
- Cálculo de downtime
- Módulo de calibraciones

### Cambiado
- Reestructuración completa de directorios (assets, config, database, src)
- CSS modular con @import (5 archivos)
- Refactor legacy → src/ (17 archivos legacy reducidos en 1967 líneas)
- Migraciones numeradas (001-024)

### Infrastructure
- gitignore mejorado
- .env.example
- Autoloader PSR-4
- PHP 8.1+ requerido
