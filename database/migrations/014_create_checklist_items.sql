CREATE TABLE IF NOT EXISTS checklist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT NOT NULL,
    orden_ejecucion INT NOT NULL,
    descripcion_tarea TEXT NOT NULL,
    es_obligatorio BOOLEAN DEFAULT TRUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_checklist_items_checklist FOREIGN KEY (checklist_id) REFERENCES checklists(id),
    INDEX idx_checklist_items_checklist (checklist_id),
    INDEX idx_checklist_items_orden (orden_ejecucion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
