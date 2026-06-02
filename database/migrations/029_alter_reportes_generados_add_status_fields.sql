ALTER TABLE reportes_generados
ADD COLUMN estado VARCHAR(20) DEFAULT 'completado',
ADD COLUMN tamano_bytes INT NULL,
ADD COLUMN duracion_ms INT NULL,
ADD COLUMN mensaje_error TEXT NULL;

UPDATE reportes_generados SET estado = 'completado';
