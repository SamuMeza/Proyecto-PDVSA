CREATE TABLE IF NOT EXISTS instalaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo_instalacion VARCHAR(50) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_instalaciones_area FOREIGN KEY (area_id) REFERENCES areas(id),
    INDEX idx_instalaciones_area_id (area_id),
    INDEX idx_instalaciones_codigo (codigo_instalacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
