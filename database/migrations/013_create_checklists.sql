CREATE TABLE IF NOT EXISTS checklists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nivel_mantenimiento_id INT NOT NULL,
    nombre_checklist VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_checklists_nivel FOREIGN KEY (nivel_mantenimiento_id) REFERENCES niveles_mantenimiento(id),
    INDEX idx_checklists_nivel (nivel_mantenimiento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
