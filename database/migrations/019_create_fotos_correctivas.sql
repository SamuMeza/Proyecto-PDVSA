CREATE TABLE IF NOT EXISTS fotos_correctivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_correctiva_id INT NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    tamano_kb INT NOT NULL,
    tipo VARCHAR(20) DEFAULT 'durante',
    subida_por_usuario_id INT NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fotos_otc FOREIGN KEY (orden_correctiva_id) REFERENCES ordenes_correctivas(id),
    CONSTRAINT fk_fotos_subida_por FOREIGN KEY (subida_por_usuario_id) REFERENCES usuarios(id),
    INDEX idx_fotos_otc (orden_correctiva_id),
    INDEX idx_fotos_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
