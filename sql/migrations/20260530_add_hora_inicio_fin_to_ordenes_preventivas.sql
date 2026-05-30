-- Migración: Agregar hora_inicio y hora_fin a ordenes_preventivas
-- Ejecutar: mysql -u root sistema_pdvsa < sql/migrations/20260530_add_hora_inicio_fin_to_ordenes_preventivas.sql

ALTER TABLE ordenes_preventivas
  ADD COLUMN hora_inicio TIME NULL AFTER fecha_planificada,
  ADD COLUMN hora_fin TIME NULL AFTER hora_inicio,
  ADD COLUMN codigo_otp_validacion VARCHAR(6) NULL AFTER motivo_suspension;
