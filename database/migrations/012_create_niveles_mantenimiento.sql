CREATE TABLE IF NOT EXISTS niveles_mantenimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre_nivel VARCHAR(50) NOT NULL,
    frecuencia VARCHAR(50) NOT NULL,
    duracion_estimada_horas DECIMAL(4,2) NOT NULL,
    cantidad_ejecutores_requeridos INT DEFAULT 1,
    descripcion TEXT NULL,
    es_automatico BOOLEAN DEFAULT TRUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_niveles_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_equipo(id),
    INDEX idx_niveles_categoria (categoria_id),
    INDEX idx_niveles_frecuencia (frecuencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
