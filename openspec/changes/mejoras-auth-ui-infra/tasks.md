## 1. Base de Datos e Infraestructura

- [ ] 1.1 Crear script de migración SQL para la tabla `usuario_otp` y configuraciones adicionales de sistema.
- [ ] 1.2 Ejecutar la migración en la base de datos local.
- [ ] 1.3 Agregar parámetros iniciales (ej. `ruta_logo_pdvsa`) en la tabla `configuracion_sistema` si no existen.

## 2. Lógica de Autenticación y OTP

- [ ] 2.1 Modificar `auth/auth_functions.php` para dar soporte a la generación y guardado de OTP, y validación de expiración.
- [ ] 2.2 Implementar en `auth/auth_functions.php` la verificación de límite de 150 OTPs diarios por usuario.
- [ ] 2.3 Crear/actualizar la interfaz de login en `auth/login.php` para incorporar el primer paso de credenciales de usuario.
- [ ] 2.4 Crear `auth/otp_verify.php` para el segundo paso del inicio de sesión (ingreso del código OTP).

## 3. Control de Acceso (RBAC) y Permisos

- [ ] 3.1 Crear función helper para validar permisos decodificando el campo `permisos_json` de la sesión.
- [ ] 3.2 Proteger las páginas principales del sistema verificando los permisos específicos requeridos.

## 4. UI Layout Premium y Tematización

- [ ] 4.1 Diseñar hoja de estilos CSS en `css/styles.css` con variables CSS para temas claro y oscuro.
- [ ] 4.2 Crear layout base en PHP (header, responsive sidebar con logotipo dinámico, footer) y conectarlo a las páginas.
- [ ] 4.3 Implementar toggle de cambio de tema e interactividad del sidebar (colapsar/expandir) con transiciones suaves en JS.
