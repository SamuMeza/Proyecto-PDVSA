## ADDED Requirements

### Requirement: Layout Base Responsivo y Soporte de Temas
El sistema SHALL proveer una plantilla base responsiva (layout) con un sidebar lateral de navegación, una cabecera para perfil/acciones, y soporte nativo para temas claro/oscuro.

#### Scenario: Cambio de tema claro a oscuro
- **WHEN** el usuario hace clic en el botón de alternancia de tema (Theme Toggle)
- **THEN** el sistema debe aplicar la clase de tema oscuro al elemento contenedor principal (`body` o `html`) y almacenar la preferencia del tema en LocalStorage o cookie.

### Requirement: Logotipo Dinámico del Sistema
El sistema SHALL cargar el logotipo de la aplicación de manera dinámica utilizando la ruta configurada en la tabla `configuracion_sistema` bajo la clave `ruta_logo_pdvsa`.

#### Scenario: Carga correcta del logo en cabecera o login
- **WHEN** se renderiza la página de login o el sidebar principal del sistema
- **THEN** el sistema debe consultar el parámetro `ruta_logo_pdvsa` y renderizar la imagen correspondiente en la interfaz de usuario.
