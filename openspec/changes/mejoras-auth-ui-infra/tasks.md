## 1. Base de Datos e Infraestructura

- [x] 1.1 Crear script de migración SQL para la tabla `usuario_otp` y configuraciones adicionales de sistema.
- [x] 1.2 Ejecutar la migración en la base de datos local.
- [x] 1.3 Agregar parámetros iniciales (ej. `ruta_logo_pdvsa`) en la tabla `configuracion_sistema` si no existen.

## 2. Lógica de Autenticación y OTP

- [x] 2.1 Modificar `auth/auth_functions.php` para dar soporte a la generación y guardado de OTP, y validación de expiración.
- [x] 2.2 Implementar en `auth/auth_functions.php` la verificación de límite de 150 OTPs diarios por usuario.
- [x] 2.3 Crear/actualizar la interfaz de login en `auth/login.php` para incorporar el primer paso de credenciales de usuario.
- [x] 2.4 Crear `auth/otp_verify.php` para el segundo paso del inicio de sesión (ingreso del código OTP).

## 3. Control de Acceso (RBAC) y Permisos

- [x] 3.1 Crear función helper para validar permisos decodificando el campo `permisos_json` de la sesión.
- [x] 3.2 Proteger las páginas principales del sistema verificando los permisos específicos requeridos.

## 4. UI Layout Premium y Tematización

- [x] 4.1 Diseñar hoja de estilos CSS en `css/styles.css` con variables CSS para temas claro y oscuro.
- [x] 4.2 Crear layout base en PHP (header, responsive sidebar con logotipo dinámico, footer) y conectarlo a las páginas.
- [x] 4.3 Implementar toggle de cambio de tema e interactividad del sidebar (colapsar/expandir) con transiciones suaves en JS.
