-- Esquema PostgreSQL para sistema_pdvsa
-- Ejecutar: psql -U postgres -f sql/schema_postgresql.sql

CREATE DATABASE sistema_pdvsa
  WITH ENCODING 'UTF8'
  LC_COLLATE = 'en_US.UTF-8'
  LC_CTYPE = 'en_US.UTF-8'
  TEMPLATE template0;

\c sistema_pdvsa;

-- Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    permisos_json JSONB DEFAULT '{}',
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_roles_nombre ON roles(nombre);

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    rol_id INTEGER NOT NULL REFERENCES roles(id),
    nombre_completo VARCHAR(200) NOT NULL,
    cargo VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    telefono_extension VARCHAR(50) NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    creado_por INTEGER NULL REFERENCES usuarios(id),
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    sesion_activa_token VARCHAR(255) NULL,
    sesion_expira_en TIMESTAMP NULL,
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_usuarios_nombre_usuario ON usuarios(nombre_usuario);
CREATE INDEX IF NOT EXISTS idx_usuarios_rol ON usuarios(rol_id);
CREATE INDEX IF NOT EXISTS idx_usuarios_estado ON usuarios(estado);
CREATE INDEX IF NOT EXISTS idx_usuarios_sesion_token ON usuarios(sesion_activa_token);

-- Roles iniciales
INSERT INTO roles (nombre, permisos_json, descripcion) VALUES
('Administrador', '{}', 'Gestión total del sistema'),
('Planificador/Programador', '{}', 'Configura frecuencias, genera calendario'),
('Supervisor', '{}', 'Asigna, audita, desbloquea, ve reportes'),
('Mantenedor', '{}', 'Ejecuta, cierra, reporta fallas')
ON CONFLICT (nombre) DO NOTHING;

-- Usuario administrador por defecto
-- Usuario: admin  |  Contraseña: Admin2026!
INSERT INTO usuarios (rol_id, nombre_completo, cargo, email, nombre_usuario, contrasena_hash, estado)
SELECT r.id, 'Administrador del Sistema', 'Administrador', 'admin@sistema.local', 'admin',
       '$2y$10$UBjLoQSH2RNAbXTQrDXohuIRB2u7RMqtSf./nKz4YbYDDcd5gERTy', 'activo'
FROM roles r
WHERE r.nombre = 'Administrador'
  AND NOT EXISTS (SELECT 1 FROM usuarios WHERE nombre_usuario = 'admin');
