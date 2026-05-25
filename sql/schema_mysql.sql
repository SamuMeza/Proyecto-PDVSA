-- Esquema MySQL para sistema_pdvsa
-- Ejecutar: mysql -u root < sql/schema_mysql.sql

CREATE DATABASE IF NOT EXISTS sistema_pdvsa
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sistema_pdvsa;

-- Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    permisos_json JSON DEFAULT (JSON_OBJECT()),
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_roles_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    cargo VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    telefono_extension VARCHAR(50) NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    creado_por INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sesion_activa_token VARCHAR(255) NULL,
    sesion_expira_en TIMESTAMP NULL,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id),
    CONSTRAINT fk_usuarios_creado_por FOREIGN KEY (creado_por) REFERENCES usuarios(id),
    INDEX idx_usuarios_nombre_usuario (nombre_usuario),
    INDEX idx_usuarios_rol (rol_id),
    INDEX idx_usuarios_estado (estado),
    INDEX idx_usuarios_sesion_token (sesion_activa_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles iniciales del sistema
INSERT INTO roles (nombre, permisos_json, descripcion) VALUES
('Administrador', '{}', 'Gestión total del sistema'),
('Planificador/Programador', '{}', 'Configura frecuencias, genera calendario'),
('Supervisor', '{}', 'Asigna, audita, desbloquea, ve reportes'),
('Mantenedor', '{}', 'Ejecuta, cierra, reporta fallas')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Usuario administrador por defecto (cambiar contraseña tras el primer acceso)
-- Usuario: admin  |  Contraseña: Admin2026!
INSERT INTO usuarios (rol_id, nombre_completo, cargo, email, nombre_usuario, contrasena_hash, estado)
SELECT r.id, 'Administrador del Sistema', 'Administrador', 'admin@sistema.local', 'admin',
       '$2y$10$UBjLoQSH2RNAbXTQrDXohuIRB2u7RMqtSf./nKz4YbYDDcd5gERTy', 'activo'
FROM roles r
WHERE r.nombre = 'Administrador'
  AND NOT EXISTS (SELECT 1 FROM usuarios WHERE nombre_usuario = 'admin');
