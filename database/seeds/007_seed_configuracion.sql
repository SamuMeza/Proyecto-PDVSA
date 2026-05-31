INSERT INTO configuracion_sistema (clave, valor, descripcion) VALUES
('ruta_logo_pdvsa', '/public/assets/images/logo-pdvsa.png', 'Ruta del logo PDVSA'),
('email_dominio_interno', 'pdvsa.com', 'Dominio para correos internos'),
('sesion_minutos_admin', '10', 'Duración de sesión Admin'),
('sesion_minutos_supervisor', '20', 'Duración de sesión Supervisor'),
('sesion_minutos_otros', '35', 'Duración de sesión otros roles')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);
