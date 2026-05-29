-- Migración para agregar soporte de OTP y configuración del sistema
-- Ejecutar en MySQL: mysql -u root < sql/migrations/20260529_add_usuario_otp_and_configuracion.sql

USE sistema_pdvsa;

-- Tabla de configuración del sistema
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NULL,
    descripcion TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de OTP para autenticación de dos factores
CREATE TABLE IF NOT EXISTS usuario_otp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    codigo VARCHAR(6) NOT NULL,
    expiracion_en TIMESTAMP NOT NULL,
    generados_hoy INT DEFAULT 0,
    fecha_ultimo_generado DATE NULL,
    intentos_fallidos INT DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_otp_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Valores iniciales de configuración
INSERT INTO configuracion_sistema (clave, valor, descripcion)
VALUES
('ruta_logo_pdvsa', '/public/assets/pdvsa-logo.svg', 'Ruta del logo PDVSA mostrado en el layout'),
('email_dominio_interno', 'pdvsa.com', 'Dominio utilizado para generar correos internos de usuario'),
('sesion_minutos_admin', '10', 'Duración de sesión en minutos para Admin'),
('sesion_minutos_supervisor', '20', 'Duración de sesión en minutos para Supervisor'),
('sesion_minutos_otros', '35', 'Duración de sesión en minutos para otros roles')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);
