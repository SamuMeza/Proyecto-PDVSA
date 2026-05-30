-- Migration: Add zona_id to ordenes_correctivas, orden_correctiva_id to ejecucion_checklist_items

ALTER TABLE ordenes_correctivas
  ADD COLUMN zona_id INT NULL AFTER prioridad_id;

ALTER TABLE ordenes_correctivas
  ADD CONSTRAINT fk_otc_zona FOREIGN KEY (zona_id) REFERENCES zonas(id);

CREATE INDEX idx_otc_zona ON ordenes_correctivas(zona_id);

ALTER TABLE ejecucion_checklist_items
  MODIFY COLUMN ejecucion_preventiva_id INT NULL;

ALTER TABLE ejecucion_checklist_items
  ADD COLUMN orden_correctiva_id INT NULL AFTER ejecucion_preventiva_id;

ALTER TABLE ejecucion_checklist_items
  ADD CONSTRAINT fk_ejecucion_items_orden_correctiva FOREIGN KEY (orden_correctiva_id) REFERENCES ordenes_correctivas(id) ON DELETE SET NULL;

CREATE INDEX idx_ejecucion_items_otc ON ejecucion_checklist_items(orden_correctiva_id);
