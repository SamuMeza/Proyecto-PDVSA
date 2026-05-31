-- Migration: Add zona_id to ordenes_correctivas, orden_correctiva_id to ejecucion_checklist_items
-- Idempotente: solo agrega columna/constraint/index si no existe aún.
-- Ejecutar: mysql -u root sistema_pdvsa < database\migrations\028_add_zona_id_otc_checklist_items.sql

SET @db := COALESCE(DATABASE(), 'sistema_pdvsa');

-- =============================================================
-- 1. ordenes_correctivas: ADD COLUMN zona_id
-- =============================================================
SET @existe := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ordenes_correctivas' AND COLUMN_NAME = 'zona_id');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ordenes_correctivas ADD COLUMN zona_id INT NULL AFTER prioridad_id',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================================
-- 2. ordenes_correctivas: ADD CONSTRAINT fk_otc_zona
-- =============================================================
SET @existe := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = @db AND TABLE_NAME = 'ordenes_correctivas' AND CONSTRAINT_NAME = 'fk_otc_zona');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ordenes_correctivas ADD CONSTRAINT fk_otc_zona FOREIGN KEY (zona_id) REFERENCES zonas(id)',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================================
-- 3. ordenes_correctivas: CREATE INDEX idx_otc_zona
-- =============================================================
SET @existe := (SELECT COUNT(*) FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ordenes_correctivas' AND INDEX_NAME = 'idx_otc_zona');
SET @sql := IF(@existe = 0,
    'CREATE INDEX idx_otc_zona ON ordenes_correctivas(zona_id)',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================================
-- 4. ejecucion_checklist_items: MODIFY COLUMN (siempre seguro)
-- =============================================================
ALTER TABLE ejecucion_checklist_items MODIFY COLUMN ejecucion_preventiva_id INT NULL;

-- =============================================================
-- 5. ejecucion_checklist_items: ADD COLUMN orden_correctiva_id
-- =============================================================
SET @existe := (SELECT COUNT(*) FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ejecucion_checklist_items' AND COLUMN_NAME = 'orden_correctiva_id');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ejecucion_checklist_items ADD COLUMN orden_correctiva_id INT NULL AFTER ejecucion_preventiva_id',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================================
-- 6. ejecucion_checklist_items: ADD CONSTRAINT fk_ejecucion_items_orden_correctiva
-- =============================================================
SET @existe := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = @db AND TABLE_NAME = 'ejecucion_checklist_items' AND CONSTRAINT_NAME = 'fk_ejecucion_items_orden_correctiva');
SET @sql := IF(@existe = 0,
    'ALTER TABLE ejecucion_checklist_items ADD CONSTRAINT fk_ejecucion_items_orden_correctiva FOREIGN KEY (orden_correctiva_id) REFERENCES ordenes_correctivas(id) ON DELETE SET NULL',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =============================================================
-- 7. ejecucion_checklist_items: CREATE INDEX idx_ejecucion_items_otc
-- =============================================================
SET @existe := (SELECT COUNT(*) FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'ejecucion_checklist_items' AND INDEX_NAME = 'idx_ejecucion_items_otc');
SET @sql := IF(@existe = 0,
    'CREATE INDEX idx_ejecucion_items_otc ON ejecucion_checklist_items(orden_correctiva_id)',
    'SELECT 1 AS _ok');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
