CREATE TABLE IF NOT EXISTS prioridades_falla (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    color_alert VARCHAR(7) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prioridades_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
