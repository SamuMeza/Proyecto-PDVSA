CREATE TABLE IF NOT EXISTS reportes_generados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte VARCHAR(50) NOT NULL,
    formato VARCHAR(10) DEFAULT 'pdf',
    generado_por_usuario_id INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    parametros_filtros_json JSON NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_archivo_descarga VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reportes_usuario FOREIGN KEY (generado_por_usuario_id) REFERENCES usuarios(id),
    INDEX idx_reportes_usuario (generado_por_usuario_id),
    INDEX idx_reportes_tipo (tipo_reporte),
    INDEX idx_reportes_fecha (fecha_generacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
