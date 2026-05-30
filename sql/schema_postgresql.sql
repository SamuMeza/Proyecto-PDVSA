-- Esquema PostgreSQL para sistema_pdvsa
-- Ejecutar: psql -U postgres -f sql/schema_postgresql.sql

CREATE DATABASE sistema_pdvsa
  WITH ENCODING 'UTF8'
  LC_COLLATE = 'en_US.UTF-8'
  LC_CTYPE = 'en_US.UTF-8'
  TEMPLATE template0;

\c sistema_pdvsa;

-- Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    permisos_json JSONB DEFAULT '{}',
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_roles_nombre ON roles(nombre);

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    rol_id INTEGER NOT NULL REFERENCES roles(id),
    nombre_completo VARCHAR(200) NOT NULL,
    cargo VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    telefono_extension VARCHAR(50) NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    creado_por INTEGER NULL REFERENCES usuarios(id),
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    sesion_activa_token VARCHAR(255) NULL,
    sesion_expira_en TIMESTAMP NULL,
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_usuarios_nombre_usuario ON usuarios(nombre_usuario);
CREATE INDEX IF NOT EXISTS idx_usuarios_rol ON usuarios(rol_id);
CREATE INDEX IF NOT EXISTS idx_usuarios_estado ON usuarios(estado);
CREATE INDEX IF NOT EXISTS idx_usuarios_sesion_token ON usuarios(sesion_activa_token);

-- Tabla de configuración del sistema
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id SERIAL PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion TEXT NULL,
    modificable_por VARCHAR(50) DEFAULT 'admin',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_config_clave ON configuracion_sistema(clave);

-- Tabla: localidades
CREATE TABLE IF NOT EXISTS localidades (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_localidades_nombre ON localidades(nombre);

-- Tabla: areas
CREATE TABLE IF NOT EXISTS areas (
    id SERIAL PRIMARY KEY,
    localidad_id INTEGER NOT NULL REFERENCES localidades(id),
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_areas_localidad_id ON areas(localidad_id);
CREATE INDEX IF NOT EXISTS idx_areas_nombre ON areas(nombre);

-- Tabla: instalaciones
CREATE TABLE IF NOT EXISTS instalaciones (
    id SERIAL PRIMARY KEY,
    area_id INTEGER NOT NULL REFERENCES areas(id),
    nombre VARCHAR(100) NOT NULL,
    codigo_instalacion VARCHAR(50) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_instalaciones_area_id ON instalaciones(area_id);
CREATE INDEX IF NOT EXISTS idx_instalaciones_codigo ON instalaciones(codigo_instalacion);

-- Tabla: zonas
CREATE TABLE IF NOT EXISTS zonas (
    id SERIAL PRIMARY KEY,
    instalacion_id INTEGER NOT NULL REFERENCES instalaciones(id),
    nombre VARCHAR(100) NOT NULL,
    codigo_zona VARCHAR(50) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_zonas_instalacion_id ON zonas(instalacion_id);
CREATE INDEX IF NOT EXISTS idx_zonas_codigo ON zonas(codigo_zona);

-- Tabla: categorias_equipo
CREATE TABLE IF NOT EXISTS categorias_equipo (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    color_calendario VARCHAR(7) DEFAULT '#7BA7D9',
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_categorias_nombre ON categorias_equipo(nombre);

-- Tabla: grupos_seguridad
CREATE TABLE IF NOT EXISTS grupos_seguridad (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_grupos_nombre ON grupos_seguridad(nombre);

-- Tabla: niveles_mantenimiento
CREATE TABLE IF NOT EXISTS niveles_mantenimiento (
    id SERIAL PRIMARY KEY,
    categoria_id INTEGER NOT NULL REFERENCES categorias_equipo(id),
    nombre_nivel VARCHAR(50) NOT NULL,
    frecuencia VARCHAR(50) NOT NULL,
    duracion_estimada_horas DECIMAL(4,2) NOT NULL,
    cantidad_ejecutores_requeridos INTEGER DEFAULT 1,
    descripcion TEXT NULL,
    es_automatico BOOLEAN DEFAULT TRUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_niveles_categoria ON niveles_mantenimiento(categoria_id);
CREATE INDEX IF NOT EXISTS idx_niveles_frecuencia ON niveles_mantenimiento(frecuencia);

-- Tabla: checklists
CREATE TABLE IF NOT EXISTS checklists (
    id SERIAL PRIMARY KEY,
    nivel_mantenimiento_id INTEGER NOT NULL REFERENCES niveles_mantenimiento(id),
    nombre_checklist VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_checklists_nivel ON checklists(nivel_mantenimiento_id);

-- Tabla: checklist_items
CREATE TABLE IF NOT EXISTS checklist_items (
    id SERIAL PRIMARY KEY,
    checklist_id INTEGER NOT NULL REFERENCES checklists(id),
    orden_ejecucion INTEGER NOT NULL,
    descripcion_tarea TEXT NOT NULL,
    es_obligatorio BOOLEAN DEFAULT TRUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_checklist_items_checklist ON checklist_items(checklist_id);
CREATE INDEX IF NOT EXISTS idx_checklist_items_orden ON checklist_items(orden_ejecucion);

-- Tabla: tipos_falla
CREATE TABLE IF NOT EXISTS tipos_falla (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_tipos_falla_nombre ON tipos_falla(nombre);

-- Tabla: prioridades_falla
CREATE TABLE IF NOT EXISTS prioridades_falla (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    color_alert VARCHAR(7) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_prioridades_nombre ON prioridades_falla(nombre);

-- Tabla: equipos
CREATE TABLE IF NOT EXISTS equipos (
    id SERIAL PRIMARY KEY,
    numero_activo_fijo VARCHAR(100) NOT NULL UNIQUE,
    serial VARCHAR(100) NULL,
    nombre VARCHAR(200) NOT NULL,
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    descripcion TEXT NULL,
    categoria_id INTEGER NOT NULL REFERENCES categorias_equipo(id),
    zona_id INTEGER NOT NULL REFERENCES zonas(id),
    grupo_responsable VARCHAR(100) NULL,
    grupo_seguridad_id INTEGER NULL REFERENCES grupos_seguridad(id),
    estado VARCHAR(50) DEFAULT 'activo',
    foto_equipo VARCHAR(255) NULL,
    fecha_registro DATE DEFAULT NOW(),
    registrado_por_usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
    ultima_modificacion TIMESTAMP DEFAULT NOW(),
    modificado_por_usuario_id INTEGER NULL REFERENCES usuarios(id),
    esta_bloqueado BOOLEAN DEFAULT FALSE,
    motivo_bloqueo TEXT NULL,
    bloqueado_por_usuario_id INTEGER NULL REFERENCES usuarios(id),
    fecha_bloqueo TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_equipos_numero_activo ON equipos(numero_activo_fijo);
CREATE INDEX IF NOT EXISTS idx_equipos_nombre ON equipos(nombre);
CREATE INDEX IF NOT EXISTS idx_equipos_categoria ON equipos(categoria_id);
CREATE INDEX IF NOT EXISTS idx_equipos_zona ON equipos(zona_id);
CREATE INDEX IF NOT EXISTS idx_equipos_estado ON equipos(estado);
CREATE INDEX IF NOT EXISTS idx_equipos_bloqueado ON equipos(esta_bloqueado);

-- Tabla: ordenes_preventivas
CREATE TABLE IF NOT EXISTS ordenes_preventivas (
    id SERIAL PRIMARY KEY,
    codigo_unico VARCHAR(50) NOT NULL UNIQUE,
    equipo_id INTEGER NOT NULL REFERENCES equipos(id),
    nivel_mantenimiento_id INTEGER NOT NULL REFERENCES niveles_mantenimiento(id),
    fecha_planificada DATE NOT NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    fecha_asignacion DATE NULL,
    fecha_inicio_ejecucion TIMESTAMP NULL,
    fecha_cierre_ejecucion TIMESTAMP NULL,
    estado VARCHAR(50) DEFAULT 'planificada',
    planificador_id INTEGER NOT NULL REFERENCES usuarios(id),
    supervisor_asigno_id INTEGER NULL REFERENCES usuarios(id),
    mantenedor_id INTEGER NULL REFERENCES usuarios(id),
    duracion_estimada_horas DECIMAL(4,2) NOT NULL,
    duracion_real_horas DECIMAL(4,2) NULL,
    observaciones_mantenedor TEXT NULL,
    observaciones_supervisor TEXT NULL,
    motivo_suspension TEXT NULL,
    descripcion TEXT NULL,
    codigo_otp_validacion VARCHAR(6) NULL,
    generada_automaticamente BOOLEAN DEFAULT TRUE,
    creada_en TIMESTAMP DEFAULT NOW(),
    actualizada_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_otp_codigo ON ordenes_preventivas(codigo_unico);
CREATE INDEX IF NOT EXISTS idx_otp_equipo ON ordenes_preventivas(equipo_id);
CREATE INDEX IF NOT EXISTS idx_otp_estado ON ordenes_preventivas(estado);
CREATE INDEX IF NOT EXISTS idx_otp_fecha_planificada ON ordenes_preventivas(fecha_planificada);
CREATE INDEX IF NOT EXISTS idx_otp_mantenedor ON ordenes_preventivas(mantenedor_id);

-- Tabla: ejecuciones_preventivas
CREATE TABLE IF NOT EXISTS ejecuciones_preventivas (
    id SERIAL PRIMARY KEY,
    orden_preventiva_id INTEGER NOT NULL REFERENCES ordenes_preventivas(id),
    mantenedor_id INTEGER NOT NULL REFERENCES usuarios(id),
    fecha_inicio_real TIMESTAMP NULL,
    fecha_fin_real TIMESTAMP NULL,
    observaciones_mantenedor TEXT NULL,
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_ejecuciones_otp ON ejecuciones_preventivas(orden_preventiva_id);
CREATE INDEX IF NOT EXISTS idx_ejecuciones_mantenedor ON ejecuciones_preventivas(mantenedor_id);

-- Tabla: ordenes_correctivas
CREATE TABLE IF NOT EXISTS ordenes_correctivas (
    id SERIAL PRIMARY KEY,
    codigo_unico VARCHAR(50) NOT NULL UNIQUE,
    equipo_id INTEGER NOT NULL REFERENCES equipos(id),
    tipo_falla_id INTEGER NOT NULL REFERENCES tipos_falla(id),
    prioridad_id INTEGER NOT NULL REFERENCES prioridades_falla(id),
    zona_id INTEGER NULL REFERENCES zonas(id),
    fecha_reporte DATE NOT NULL,
    hora_reporte TIME NOT NULL,
    reportado_por_usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
    fecha_inicio_reparacion DATE NULL,
    hora_inicio_reparacion TIME NULL,
    fecha_fin_reparacion DATE NULL,
    hora_fin_reparacion TIME NULL,
    descripcion_falla TEXT NOT NULL,
    acciones_tomadas TEXT NULL,
    causa_raiz TEXT NULL,
    repuestos_utilizados TEXT NULL,
    downtime_calculado_minutos INTEGER NULL,
    estado VARCHAR(50) DEFAULT 'reportada',
    supervisor_asigno_id INTEGER NULL REFERENCES usuarios(id),
    mantenedor_id INTEGER NULL REFERENCES usuarios(id),
    cerrada_por_usuario_id INTEGER NULL REFERENCES usuarios(id),
    fecha_cierre TIMESTAMP NULL,
    es_preventivo_fuera_de_plan BOOLEAN DEFAULT FALSE,
    es_reporte_condicion BOOLEAN DEFAULT FALSE,
    condicion_observada TEXT NULL,
    recomendacion_condicion TEXT NULL,
    orden_preventiva_relacionada_id INTEGER NULL REFERENCES ordenes_preventivas(id),
    orden_correctiva_relacionada_id INTEGER NULL REFERENCES ordenes_correctivas(id),
    creada_en TIMESTAMP DEFAULT NOW(),
    actualizada_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_otc_codigo ON ordenes_correctivas(codigo_unico);
CREATE INDEX IF NOT EXISTS idx_otc_equipo ON ordenes_correctivas(equipo_id);
CREATE INDEX IF NOT EXISTS idx_otc_estado ON ordenes_correctivas(estado);
CREATE INDEX IF NOT EXISTS idx_otc_fecha_reporte ON ordenes_correctivas(fecha_reporte);
CREATE INDEX IF NOT EXISTS idx_otc_mantenedor ON ordenes_correctivas(mantenedor_id);
CREATE INDEX IF NOT EXISTS idx_otc_es_condicion ON ordenes_correctivas(es_reporte_condicion);

-- Tabla: fotos_correctivas
CREATE TABLE IF NOT EXISTS fotos_correctivas (
    id SERIAL PRIMARY KEY,
    orden_correctiva_id INTEGER NOT NULL REFERENCES ordenes_correctivas(id),
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    tamano_kb INTEGER NOT NULL,
    tipo VARCHAR(20) DEFAULT 'durante',
    subida_por_usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
    fecha_subida TIMESTAMP DEFAULT NOW(),
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_fotos_otc ON fotos_correctivas(orden_correctiva_id);
CREATE INDEX IF NOT EXISTS idx_fotos_tipo ON fotos_correctivas(tipo);

-- Tabla: ejecucion_checklist_items
CREATE TABLE IF NOT EXISTS ejecucion_checklist_items (
    id SERIAL PRIMARY KEY,
    ejecucion_preventiva_id INTEGER NULL REFERENCES ejecuciones_preventivas(id) ON DELETE SET NULL,
    orden_correctiva_id INTEGER NULL REFERENCES ordenes_correctivas(id) ON DELETE SET NULL,
    checklist_item_id INTEGER NOT NULL REFERENCES checklist_items(id),
    marcado_como_hecho BOOLEAN DEFAULT FALSE,
    observacion_item TEXT NULL,
    fecha_marcado TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_ejecucion_items_ejecucion ON ejecucion_checklist_items(ejecucion_preventiva_id);
CREATE INDEX IF NOT EXISTS idx_ejecucion_items_otc ON ejecucion_checklist_items(orden_correctiva_id);
CREATE INDEX IF NOT EXISTS idx_ejecucion_items_checklist ON ejecucion_checklist_items(checklist_item_id);

-- Tabla: calibraciones
CREATE TABLE IF NOT EXISTS calibraciones (
    id SERIAL PRIMARY KEY,
    equipo_id INTEGER NOT NULL REFERENCES equipos(id),
    tipo_calibracion VARCHAR(50) NOT NULL,
    fecha_ultima_calibracion DATE NOT NULL,
    fecha_proxima_calibracion DATE NOT NULL,
    entidad_certificadora VARCHAR(200) NULL,
    certificado_ruta VARCHAR(255) NULL,
    rango_medicion VARCHAR(100) NULL,
    error_permitido VARCHAR(50) NULL,
    observaciones TEXT NULL,
    registrado_por_usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
    estado VARCHAR(50) DEFAULT 'al_dia',
    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_calibraciones_equipo ON calibraciones(equipo_id);
CREATE INDEX IF NOT EXISTS idx_calibraciones_fecha_proxima ON calibraciones(fecha_proxima_calibracion);
CREATE INDEX IF NOT EXISTS idx_calibraciones_estado ON calibraciones(estado);

-- Tabla: reportes_generados
CREATE TABLE IF NOT EXISTS reportes_generados (
    id SERIAL PRIMARY KEY,
    tipo_reporte VARCHAR(50) NOT NULL,
    formato VARCHAR(10) DEFAULT 'pdf',
    generado_por_usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
    fecha_generacion TIMESTAMP DEFAULT NOW(),
    parametros_filtros_json JSONB NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_archivo_descarga VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_reportes_usuario ON reportes_generados(generado_por_usuario_id);
CREATE INDEX IF NOT EXISTS idx_reportes_tipo ON reportes_generados(tipo_reporte);
CREATE INDEX IF NOT EXISTS idx_reportes_fecha ON reportes_generados(fecha_generacion);

-- Tabla: logs_auditoria
CREATE TABLE IF NOT EXISTS logs_auditoria (
    id BIGSERIAL PRIMARY KEY,
    usuario_id INTEGER NULL REFERENCES usuarios(id),
    accion VARCHAR(20) NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_afectado_id INTEGER NULL,
    datos_anteriores_json JSONB NULL,
    datos_nuevos_json JSONB NULL,
    direccion_ip VARCHAR(45) NULL,
    fecha_hora TIMESTAMP DEFAULT NOW(),
    descripcion TEXT NULL
);

CREATE INDEX IF NOT EXISTS idx_logs_usuario ON logs_auditoria(usuario_id);
CREATE INDEX IF NOT EXISTS idx_logs_accion ON logs_auditoria(accion);
CREATE INDEX IF NOT EXISTS idx_logs_tabla ON logs_auditoria(tabla_afectada);
CREATE INDEX IF NOT EXISTS idx_logs_fecha ON logs_auditoria(fecha_hora);
CREATE INDEX IF NOT EXISTS idx_logs_registro ON logs_auditoria(tabla_afectada, registro_afectado_id);

-- Tabla: alertas
CREATE TABLE IF NOT EXISTS alertas (
    id SERIAL PRIMARY KEY,
    usuario_destino_id INTEGER NOT NULL REFERENCES usuarios(id),
    tipo_alerta VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    entidad_relacionada_tipo VARCHAR(20) NULL,
    entidad_relacionada_id INTEGER NULL,
    fecha_generacion TIMESTAMP DEFAULT NOW(),
    fecha_vencimiento_referencia DATE NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_lectura TIMESTAMP NULL,
    accion_tomada VARCHAR(20) NULL,
    creado_en TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_alertas_usuario ON alertas(usuario_destino_id);
CREATE INDEX IF NOT EXISTS idx_alertas_tipo ON alertas(tipo_alerta);
CREATE INDEX IF NOT EXISTS idx_alertas_leida ON alertas(leida);
CREATE INDEX IF NOT EXISTS idx_alertas_fecha ON alertas(fecha_generacion);
