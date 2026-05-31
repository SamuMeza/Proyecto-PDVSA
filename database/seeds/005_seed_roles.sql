INSERT INTO roles (nombre, permisos_json, descripcion) VALUES
('Administrador', '{}', 'Gestión total del sistema'),
('Planificador/Programador', '{}', 'Configura frecuencias, genera calendario'),
('Supervisor', '{}', 'Asigna, audita, desbloquea, ve reportes'),
('Mantenedor', '{}', 'Ejecuta, cierra, reporta fallas')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
