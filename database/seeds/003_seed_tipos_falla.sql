INSERT INTO tipos_falla (nombre, descripcion) VALUES
('Mecánica', 'Fallas de origen mecánico'),
('Eléctrica', 'Fallas de origen eléctrico'),
('Instrumentación', 'Fallas en instrumentos y sensores'),
('Fuga', 'Fugas de fluido o gas'),
('Vibración', 'Vibración anormal'),
('Ruido', 'Ruido anormal'),
('Desgaste', 'Desgaste por uso normal'),
('Corrosión', 'Corrosión o deterioro')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
