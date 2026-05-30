## ADDED Requirements

### Requirement: Autenticación en Dos Pasos (Credenciales y OTP)
El sistema SHALL requerir que el usuario ingrese primero sus credenciales (usuario y contraseña) correctas, y posteriormente un código de verificación OTP de 6 dígitos enviado/simulado para completar el inicio de sesión.

#### Scenario: Flujo exitoso de autenticación con OTP
- **WHEN** el usuario ingresa su nombre de usuario y contraseña correctos
- **THEN** el sistema debe generar un código OTP de 6 dígitos, guardarlo en la base de datos con expiración de 10 minutos, enviarlo/mostrarlo y redirigir al usuario al formulario de verificación de OTP.

#### Scenario: Verificación exitosa del código OTP
- **WHEN** el usuario introduce el código OTP correcto antes de su expiración
- **THEN** el sistema debe marcar la sesión como completamente autenticada y redirigir al usuario al dashboard según su rol.

### Requirement: Límite Diario de Generación de OTP
El sistema SHALL limitar el número de códigos OTP que un usuario puede solicitar en un mismo día a un máximo de 150 intentos.

#### Scenario: Bloqueo por superar límite de OTP
- **WHEN** el usuario solicita un código OTP y el contador `intentos_hoy` para el día actual es mayor o igual a 150
- **THEN** el sistema debe rechazar la solicitud, denegar la generación del código y mostrar un mensaje indicando que ha superado el límite diario permitido de 150 intentos.
