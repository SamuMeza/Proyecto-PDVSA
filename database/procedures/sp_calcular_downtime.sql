DELIMITER //
CREATE PROCEDURE IF NOT EXISTS sp_calcular_downtime(IN p_orden_correctiva_id INT)
BEGIN
    DECLARE v_fecha_inicio DATE;
    DECLARE v_hora_inicio TIME;
    DECLARE v_fecha_fin DATE;
    DECLARE v_hora_fin TIME;
    DECLARE v_downtime INT;

    SELECT fecha_reporte, hora_reporte, fecha_fin_reparacion, hora_fin_reparacion
    INTO v_fecha_inicio, v_hora_inicio, v_fecha_fin, v_hora_fin
    FROM ordenes_correctivas
    WHERE id = p_orden_correctiva_id;

    IF v_fecha_fin IS NOT NULL AND v_hora_fin IS NOT NULL THEN
        SET v_downtime = TIMESTAMPDIFF(MINUTE,
            CONCAT(v_fecha_inicio, ' ', v_hora_inicio),
            CONCAT(v_fecha_fin, ' ', v_hora_fin)
        );
        IF v_downtime < 0 THEN SET v_downtime = 0; END IF;

        UPDATE ordenes_correctivas
        SET downtime_calculado_minutos = v_downtime
        WHERE id = p_orden_correctiva_id;
    END IF;
END //
DELIMITER ;
