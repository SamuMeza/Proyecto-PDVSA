-- =====================================================
-- SEED: Datos de Prueba para Sistema PDVSA
-- Ejecutar: mysql -u root sistema_pdvsa < database/seeds/009_seed_datos_prueba.sql
-- =====================================================

-- -----------------------------------------------------
-- 1. VERIFICAR DATOS EXISTENTES
-- -----------------------------------------------------
-- Las áreas e instalaciones ya existen (ids 5-8)
-- Las zonas ya existen (ids 6-15)

-- -----------------------------------------------------
-- 2. EQUIPOS (usa zonas existentes ids 6-15)
-- -----------------------------------------------------
INSERT INTO equipos (numero_activo_fijo, nombre, descripcion, categoria_id, zona_id, estado, registrado_por_usuario_id) VALUES
-- Zona A - Compresores (zona_id=6)
('CMP-001', 'Compresor Atlas 1', 'Compresor de aire principal', 2, 6, 'activo', 1),
('CMP-002', 'Compresor Atlas 2', 'Compresor de aire auxiliar', 2, 6, 'activo', 1),
('CMP-003', 'Compresor Sullair', 'Compresor de respaldo', 2, 6, 'activo', 1),
-- Zona B - Bombas (zona_id=7)
('BMB-001', 'Bomba Centrífuga 1', 'Bomba de circulating water', 1, 7, 'activo', 1),
('BMB-002', 'Bomba Centrífuga 2', 'Bomba de process water', 1, 7, 'activo', 1),
('BMB-003', 'Bomba de Desazolve', 'Bomba para lodos', 1, 7, 'activo', 1),
-- Zona C - Válvulas (zona_id=8)
('VLV-001', 'Válvula de Control V-101', 'Válvula de control de flujo', 3, 8, 'activo', 1),
('VLV-002', 'Válvula de Seguridad V-201', 'Válvula de alivio de presión', 3, 8, 'activo', 1),
('VLV-003', 'Válvula Mariposa V-301', 'Válvula de aislamiento', 3, 8, 'activo', 1),
-- Zona D - Motores (zona_id=9)
('MTR-001', 'Motor Eléctrico 1', 'Motor de 100 HP', 4, 9, 'activo', 1),
('MTR-002', 'Motor Eléctrico 2', 'Motor de 50 HP', 4, 9, 'activo', 1),
('MTR-003', 'Motor de Ventilador', 'Motor de ventilación industrial', 4, 9, 'activo', 1),
-- Zona E - Tanques (zona_id=10)
('TNQ-001', 'Tanque de Almacenamiento 1', 'Tanque de 5000 galones', 6, 10, 'activo', 1),
('TNQ-002', 'Tanque de Almacenamiento 2', 'Tanque de 10000 galones', 6, 10, 'activo', 1),
('TNQ-003', 'Tanque de Proceso', 'Tanque de proceso químico', 6, 10, 'activo', 1)
ON DUPLICATE KEY UPDATE numero_activo_fijo = VALUES(numero_activo_fijo);

-- -----------------------------------------------------
-- 3. ÓRDENES PREVENTIVAS (varios estados)
-- -----------------------------------------------------
INSERT INTO ordenes_preventivas (codigo_unico, equipo_id, nivel_mantenimiento_id, fecha_planificada, hora_inicio, hora_fin, estado, descripcion, planificador_id, duracion_estimada_horas) VALUES
-- Completadas (cerradas) - equipo_id 16-30 corresponden a los insertados arriba
('OPR-2026-001', 16, 1, '2026-01-15', '08:00', '10:00', 'cerrada', 'Mantenimiento preventivo compresor Atlas 1', 1, 2.00),
('OPR-2026-002', 19, 1, '2026-01-20', '08:00', '12:00', 'cerrada', 'Mantenimiento bomba centrífuga 1', 1, 4.00),
('OPR-2026-003', 22, 2, '2026-02-05', '09:00', '11:00', 'cerrada', 'Calibración válvula de control', 1, 2.00),
('OPR-2026-004', 25, 1, '2026-02-10', '08:00', '09:30', 'cerrada', 'Mantenimiento motor eléctrico 1', 1, 1.50),
('OPR-2026-005', 28, 2, '2026-02-15', '10:00', '14:00', 'cerrada', 'Inspección tanque de almacenamiento', 1, 4.00),
('OPR-2026-006', 17, 1, '2026-03-01', '08:00', '10:00', 'cerrada', 'Mantenimiento preventivo compresor Atlas 2', 1, 2.00),
('OPR-2026-007', 20, 1, '2026-03-10', '08:00', '12:00', 'cerrada', 'Mantenimiento bomba centrífuga 2', 1, 4.00),
('OPR-2026-008', 23, 2, '2026-03-15', '09:00', '11:00', 'cerrada', 'Mantenimiento válvula de seguridad', 1, 2.00),
-- En curso
('OPR-2026-009', 18, 1, '2026-04-01', '08:00', '12:00', 'en_curso', 'Mantenimiento compresor Sullair', 1, 4.00),
('OPR-2026-010', 21, 1, '2026-04-05', '08:00', '10:00', 'en_curso', 'Mantenimiento bomba de desazolve', 1, 2.00),
-- Planificadas
('OPR-2026-011', 26, 1, '2026-05-01', '08:00', '10:00', 'planificada', 'Mantenimiento motor eléctrico 2', 1, 2.00),
('OPR-2026-012', 29, 2, '2026-05-10', '09:00', '13:00', 'planificada', 'Mantenimiento tanque de almacenamiento 2', 1, 4.00),
('OPR-2026-013', 24, 1, '2026-05-15', '08:00', '10:00', 'planificada', 'Mantenimiento válvula mariposa', 1, 2.00),
('OPR-2026-014', 27, 1, '2026-05-20', '08:00', '09:00', 'planificada', 'Mantenimiento motor ventilador', 1, 1.00),
-- Suspendidas
('OPR-2026-015', 30, 2, '2026-04-20', '10:00', '15:00', 'suspendida', 'Mantenimiento tanque de proceso - suspendido por falta de repuestos', 1, 5.00)
ON DUPLICATE KEY UPDATE codigo_unico = VALUES(codigo_unico);

-- -----------------------------------------------------
-- 4. ÓRDENES CORRECTIVAS (varios estados)
-- -----------------------------------------------------
INSERT INTO ordenes_correctivas (codigo_unico, equipo_id, tipo_falla_id, prioridad_id, zona_id, fecha_reporte, hora_reporte, reportado_por_usuario_id, mantenedor_id, estado, descripcion_falla, acciones_tomadas) VALUES
-- Cerradas (zona_id usa los IDs correctos: 6-10, equipo_id 16-30)
('OCR-2026-001', 16, 1, 1, 6, '2026-01-10', '14:30', 1, 1, 'cerrada', 'Ruido anormal en compresor', 'Se reemplazaron rodamientos'),
('OCR-2026-002', 19, 2, 2, 7, '2026-01-25', '09:15', 1, 1, 'cerrada', 'Fuga de agua en bomba', 'Se selló la fuga y se ajustó empaque'),
('OCR-2026-003', 22, 3, 1, 8, '2026-02-08', '11:00', 1, 1, 'cerrada', 'Válvula no cierra completamente', 'Se ajustó el actuador'),
('OCR-2026-004', 25, 1, 3, 9, '2026-02-12', '16:45', 1, 1, 'cerrada', 'Motor se calienta excesivamente', 'Se limpió ventilación y se verificó aislamiento'),
('OCR-2026-005', 17, 4, 2, 6, '2026-03-05', '10:30', 1, 1, 'cerrada', 'Compresor no arranca', 'Se reemplazó condensador de arranque'),
('OCR-2026-006', 20, 2, 1, 7, '2026-03-12', '08:45', 1, 1, 'cerrada', 'Vibración excesiva en bomba', 'Se alineó bomba con motor'),
('OCR-2026-007', 26, 1, 2, 9, '2026-03-20', '14:00', 1, 1, 'cerrada', 'Motor hace ruido anormal', 'Se lubricaron rodamientos'),
-- Abiertas (reportadas)
('OCR-2026-008', 18, 1, 1, 6, '2026-04-15', '10:00', 1, 1, 'reportada', 'Compresor perdiendo presión', 'Pendiente de revisión'),
('OCR-2026-009', 23, 3, 2, 8, '2026-04-18', '15:30', 1, 1, 'reportada', 'Válvula de seguridad gotea', 'Programar mantenimiento'),
('OCR-2026-010', 28, 5, 1, 10, '2026-04-20', '09:00', 1, 1, 'reportada', 'Fuga en tanque de proceso', 'Evacuar zona y reparar'),
-- En progreso
('OCR-2026-011', 21, 2, 2, 7, '2026-04-22', '11:15', 1, 1, 'en_progreso', 'Bomba no entrega caudal', 'Desmontar e inspeccionar impeler'),
('OCR-2026-012', 29, 4, 3, 10, '2026-04-25', '08:30', 1, 1, 'en_progreso', 'Tanque presenta corrosión', 'Preparar revestimiento')
ON DUPLICATE KEY UPDATE codigo_unico = VALUES(codigo_unico);

-- -----------------------------------------------------
-- 5. ACTUALIZAR FECHAS DE CIERRE PARA ÓRDENES CERRADAS
-- -----------------------------------------------------
UPDATE ordenes_preventivas SET 
    fecha_cierre_ejecucion = DATE_ADD(fecha_planificada, INTERVAL 1 DAY)
WHERE estado = 'cerrada' AND fecha_cierre_ejecucion IS NULL;

UPDATE ordenes_correctivas SET
    fecha_cierre = DATE_ADD(fecha_reporte, INTERVAL 3 DAY)
WHERE estado = 'cerrada' AND fecha_cierre IS NULL;

-- -----------------------------------------------------
-- 6. VERIFICACIÓN
-- -----------------------------------------------------
SELECT 'Equipos creados' as mensaje, COUNT(*) as total FROM equipos
UNION ALL
SELECT 'Órdenes preventivas', COUNT(*) FROM ordenes_preventivas
UNION ALL
SELECT 'Órdenes correctivas', COUNT(*) FROM ordenes_correctivas;
