CREATE TABLE IF NOT EXISTS alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_destino_id INT NOT NULL,
    tipo_alerta VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    entidad_relacionada_tipo VARCHAR(20) NULL,
    entidad_relacionada_id INT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento_referencia DATE NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_lectura TIMESTAMP NULL,
    accion_tomada VARCHAR(20) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alertas_usuario FOREIGN KEY (usuario_destino_id) REFERENCES usuarios(id),
    INDEX idx_alertas_usuario (usuario_destino_id),
    INDEX idx_alertas_tipo (tipo_alerta),
    INDEX idx_alertas_leida (leida),
    INDEX idx_alertas_fecha (fecha_generacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
