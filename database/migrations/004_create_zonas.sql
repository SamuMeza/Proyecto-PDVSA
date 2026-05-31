CREATE TABLE IF NOT EXISTS zonas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instalacion_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo_zona VARCHAR(50) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_zonas_instalacion FOREIGN KEY (instalacion_id) REFERENCES instalaciones(id),
    INDEX idx_zonas_instalacion_id (instalacion_id),
    INDEX idx_zonas_codigo (codigo_zona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
