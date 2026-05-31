INSERT INTO categorias_equipo (nombre, descripcion, color_calendario) VALUES
('Bomba Centrífuga', 'Equipos de bombeo centrífugo', '#7BA7D9'),
('Compresor', 'Compresores de aire y gas', '#A8D5BA'),
('Válvula', 'Válvulas de control y seguridad', '#F4D03F'),
('Motor Eléctrico', 'Motores eléctricos', '#E8837B'),
('Intercambiador de Calor', 'Intercambiadores de calor', '#C39BD3'),
('Tanque', 'Tanques de almacenamiento', '#85C1E9')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
