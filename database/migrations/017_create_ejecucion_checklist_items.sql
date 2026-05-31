CREATE TABLE IF NOT EXISTS ejecucion_checklist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ejecucion_preventiva_id INT NULL,
    orden_correctiva_id INT NULL,
    checklist_item_id INT NOT NULL,
    marcado_como_hecho BOOLEAN DEFAULT FALSE,
    observacion_item TEXT NULL,
    fecha_marcado TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ejecucion_items_ejecucion FOREIGN KEY (ejecucion_preventiva_id) REFERENCES ejecuciones_preventivas(id) ON DELETE SET NULL,
    CONSTRAINT fk_ejecucion_items_orden_correctiva FOREIGN KEY (orden_correctiva_id) REFERENCES ordenes_correctivas(id) ON DELETE SET NULL,
    CONSTRAINT fk_ejecucion_items_checklist FOREIGN KEY (checklist_item_id) REFERENCES checklist_items(id),
    INDEX idx_ejecucion_items_ejecucion (ejecucion_preventiva_id),
    INDEX idx_ejecucion_items_otc (orden_correctiva_id),
    INDEX idx_ejecucion_items_checklist (checklist_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
