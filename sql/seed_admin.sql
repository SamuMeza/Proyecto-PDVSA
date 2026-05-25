-- Insertar usuario administrador en base existente
-- Usuario: admin  |  Contraseña: Admin2026!
USE sistema_pdvsa;

INSERT INTO usuarios (rol_id, nombre_completo, cargo, email, nombre_usuario, contrasena_hash, estado)
SELECT r.id, 'Administrador del Sistema', 'Administrador', 'admin@sistema.local', 'admin',
       '$2y$10$UBjLoQSH2RNAbXTQrDXohuIRB2u7RMqtSf./nKz4YbYDDcd5gERTy', 'activo'
FROM roles r
WHERE r.nombre = 'Administrador'
  AND NOT EXISTS (SELECT 1 FROM usuarios WHERE nombre_usuario = 'admin');
