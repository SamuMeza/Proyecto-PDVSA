INSERT INTO niveles_mantenimiento (categoria_id, nombre_nivel, frecuencia, duracion_estimada_horas, cantidad_ejecutores_requeridos, es_automatico)
SELECT c.id, 'Mantenimiento Mensual', 'mensual', 4.0, 2, TRUE
FROM categorias_equipo c
WHERE NOT EXISTS (SELECT 1 FROM niveles_mantenimiento WHERE categoria_id = c.id AND frecuencia = 'mensual');

INSERT INTO niveles_mantenimiento (categoria_id, nombre_nivel, frecuencia, duracion_estimada_horas, cantidad_ejecutores_requeridos, es_automatico)
SELECT c.id, 'Mantenimiento Trimestral', 'trimestral', 8.0, 2, TRUE
FROM categorias_equipo c
WHERE NOT EXISTS (SELECT 1 FROM niveles_mantenimiento WHERE categoria_id = c.id AND frecuencia = 'trimestral');

INSERT INTO niveles_mantenimiento (categoria_id, nombre_nivel, frecuencia, duracion_estimada_horas, cantidad_ejecutores_requeridos, es_automatico)
SELECT c.id, 'Mantenimiento Semestral', 'semestral', 16.0, 3, TRUE
FROM categorias_equipo c
WHERE NOT EXISTS (SELECT 1 FROM niveles_mantenimiento WHERE categoria_id = c.id AND frecuencia = 'semestral');
