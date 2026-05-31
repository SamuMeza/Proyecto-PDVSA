INSERT INTO localidades (nombre) VALUES
('Punta de Mata')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
