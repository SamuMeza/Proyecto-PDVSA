CREATE TABLE IF NOT EXISTS ejecuciones_preventivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_preventiva_id INT NOT NULL,
    mantenedor_id INT NOT NULL,
    fecha_inicio_real TIMESTAMP NULL,
    fecha_fin_real TIMESTAMP NULL,
    observaciones_mantenedor TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ejecuciones_orden_preventiva FOREIGN KEY (orden_preventiva_id) REFERENCES ordenes_preventivas(id),
    CONSTRAINT fk_ejecuciones_mantenedor FOREIGN KEY (mantenedor_id) REFERENCES usuarios(id),
    INDEX idx_ejecuciones_otp (orden_preventiva_id),
    INDEX idx_ejecuciones_mantenedor (mantenedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
