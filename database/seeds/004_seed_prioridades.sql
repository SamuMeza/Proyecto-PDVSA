INSERT INTO prioridades_falla (nombre, color_alert, descripcion) VALUES
('Crítica', '#FF0000', 'Falla que requiere atención inmediata, riesgo de seguridad o parada mayor'),
('Alta', '#FF6600', 'Falla que debe atenderse en el menor tiempo posible'),
('Media', '#FFCC00', 'Falla que puede esperar hasta 48 horas'),
('Baja', '#00AA00', 'Falla que puede programarse en mantenimiento rutinario')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
