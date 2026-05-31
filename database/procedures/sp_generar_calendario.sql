DELIMITER //
CREATE PROCEDURE IF NOT EXISTS sp_generar_calendario(IN p_year INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_nivel_id INT;
    DECLARE v_categoria_id INT;
    DECLARE v_frecuencia VARCHAR(50);
    DECLARE v_nombre_nivel VARCHAR(50);
    DECLARE v_duracion DECIMAL(4,2);
    DECLARE v_ejecutores INT;
    DECLARE v_fecha_inicio DATE;
    DECLARE v_fecha_fin DATE;
    DECLARE cur CURSOR FOR
        SELECT nm.id, nm.categoria_id, nm.frecuencia, nm.nombre_nivel,
               nm.duracion_estimada_horas, nm.cantidad_ejecutores_requeridos
        FROM niveles_mantenimiento nm
        WHERE nm.estado = 'activo' AND nm.es_automatico = TRUE;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO v_nivel_id, v_categoria_id, v_frecuencia, v_nombre_nivel, v_duracion, v_ejecutores;
        IF done THEN LEAVE read_loop; END IF;

        -- Generar fechas según frecuencia
        CASE
            WHEN v_frecuencia = 'mensual' THEN
                SET v_fecha_inicio = DATE(CONCAT(p_year, '-01-01'));
                WHILE v_fecha_inicio <= DATE(CONCAT(p_year, '-12-31')) DO
                    CALL sp_insertar_orden_preventiva(v_nivel_id, v_categoria_id, v_fecha_inicio, v_duracion, v_ejecutores);
                    SET v_fecha_inicio = DATE_ADD(v_fecha_inicio, INTERVAL 1 MONTH);
                END WHILE;
            WHEN v_frecuencia = 'trimestral' THEN
                SET v_fecha_inicio = DATE(CONCAT(p_year, '-01-01'));
                WHILE v_fecha_inicio <= DATE(CONCAT(p_year, '-12-31')) DO
                    CALL sp_insertar_orden_preventiva(v_nivel_id, v_categoria_id, v_fecha_inicio, v_duracion, v_ejecutores);
                    SET v_fecha_inicio = DATE_ADD(v_fecha_inicio, INTERVAL 3 MONTH);
                END WHILE;
            WHEN v_frecuencia = 'semestral' THEN
                SET v_fecha_inicio = DATE(CONCAT(p_year, '-01-01'));
                WHILE v_fecha_inicio <= DATE(CONCAT(p_year, '-12-31')) DO
                    CALL sp_insertar_orden_preventiva(v_nivel_id, v_categoria_id, v_fecha_inicio, v_duracion, v_ejecutores);
                    SET v_fecha_inicio = DATE_ADD(v_fecha_inicio, INTERVAL 6 MONTH);
                END WHILE;
            WHEN v_frecuencia = 'anual' THEN
                SET v_fecha_inicio = DATE(CONCAT(p_year, '-01-01'));
                CALL sp_insertar_orden_preventiva(v_nivel_id, v_categoria_id, v_fecha_inicio, v_duracion, v_ejecutores);
            ELSE BEGIN END;
        END CASE;
    END LOOP;
    CLOSE cur;
END //
DELIMITER ;
