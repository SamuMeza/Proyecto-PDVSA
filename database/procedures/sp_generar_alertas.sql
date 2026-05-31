DELIMITER //
CREATE PROCEDURE IF NOT EXISTS sp_generar_alertas()
BEGIN
    -- Alertas de calibraciones vencidas
    INSERT INTO alertas (usuario_destino_id, tipo_alerta, mensaje, entidad_relacionada_tipo, entidad_relacionada_id, fecha_vencimiento_referencia)
    SELECT c.registrado_por_usuario_id, 'calibracion_vencida',
           CONCAT('Calibración vencida para equipo #', c.equipo_id),
           'calibracion', c.id, c.fecha_proxima_calibracion
    FROM calibraciones c
    WHERE c.fecha_proxima_calibracion < CURDATE() AND c.estado = 'al_dia'
      AND NOT EXISTS (
          SELECT 1 FROM alertas a
          WHERE a.entidad_relacionada_tipo = 'calibracion'
            AND a.entidad_relacionada_id = c.id
            AND a.tipo_alerta = 'calibracion_vencida'
      );

    -- Alertas de calibraciones próximas (30 días)
    INSERT INTO alertas (usuario_destino_id, tipo_alerta, mensaje, entidad_relacionada_tipo, entidad_relacionada_id, fecha_vencimiento_referencia)
    SELECT c.registrado_por_usuario_id, 'calibracion_proxima',
           CONCAT('Calibración próxima a vencer para equipo #', c.equipo_id),
           'calibracion', c.id, c.fecha_proxima_calibracion
    FROM calibraciones c
    WHERE c.fecha_proxima_calibracion BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
      AND c.estado = 'al_dia'
      AND NOT EXISTS (
          SELECT 1 FROM alertas a
          WHERE a.entidad_relacionada_tipo = 'calibracion'
            AND a.entidad_relacionada_id = c.id
            AND a.tipo_alerta = 'calibracion_proxima'
      );

    -- Alertas de órdenes preventivas vencidas
    INSERT INTO alertas (usuario_destino_id, tipo_alerta, mensaje, entidad_relacionada_tipo, entidad_relacionada_id, fecha_vencimiento_referencia)
    SELECT op.mantenedor_id, 'preventiva_vencida',
           CONCAT('Orden preventiva ', op.codigo_unico, ' está vencida'),
           'orden_preventiva', op.id, op.fecha_planificada
    FROM ordenes_preventivas op
    WHERE op.fecha_planificada < CURDATE() AND op.estado IN ('planificada', 'asignada')
      AND op.mantenedor_id IS NOT NULL
      AND NOT EXISTS (
          SELECT 1 FROM alertas a
          WHERE a.entidad_relacionada_tipo = 'orden_preventiva'
            AND a.entidad_relacionada_id = op.id
            AND a.tipo_alerta = 'preventiva_vencida'
      );
END //
DELIMITER ;
