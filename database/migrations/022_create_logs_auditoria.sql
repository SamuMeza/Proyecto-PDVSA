CREATE TABLE IF NOT EXISTS logs_auditoria (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(20) NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_afectado_id INT NULL,
    datos_anteriores_json JSON NULL,
    datos_nuevos_json JSON NULL,
    direccion_ip VARCHAR(45) NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT NULL,
    CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_logs_usuario (usuario_id),
    INDEX idx_logs_accion (accion),
    INDEX idx_logs_tabla (tabla_afectada),
    INDEX idx_logs_fecha (fecha_hora),
    INDEX idx_logs_registro (tabla_afectada, registro_afectado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
