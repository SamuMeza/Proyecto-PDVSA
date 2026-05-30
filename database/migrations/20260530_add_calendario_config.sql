-- Migration: Add calendario configuration defaults
INSERT INTO configuracion_sistema (clave, valor, descripcion, modificable_por)
VALUES ('colores_familia_calendario', '{}', 'JSON con mapeo de familia -> color hex para el calendario de mantenimiento', 'admin')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
