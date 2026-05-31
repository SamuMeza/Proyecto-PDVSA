-- Migración: Agregar hora_inicio, hora_fin y codigo_otp_validacion a ordenes_preventivas
-- Idempotente: solo agrega columnas si no existen aún.
-- Ejecutar: mysql -u root sistema_pdvsa < database\migrations\027_add_hora_inicio_fin_to_ordenes_preventivas.sql

SET @db := COALESCE(DATABASE(), 'sistema_pdvsa');

-- Columna: hora_inicio AFTER fecha_planificada
SET @existe := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ordenes_preventivas' AND COLUMN_NAME = 'hora_inicio');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ordenes_preventivas ADD COLUMN hora_inicio TIME NULL AFTER fecha_planificada',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Columna: hora_fin AFTER hora_inicio
SET @existe := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ordenes_preventivas' AND COLUMN_NAME = 'hora_fin');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ordenes_preventivas ADD COLUMN hora_fin TIME NULL AFTER hora_inicio',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Columna: codigo_otp_validacion AFTER motivo_suspension
SET @existe := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ordenes_preventivas' AND COLUMN_NAME = 'codigo_otp_validacion');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ordenes_preventivas ADD COLUMN codigo_otp_validacion VARCHAR(6) NULL AFTER motivo_suspension',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
