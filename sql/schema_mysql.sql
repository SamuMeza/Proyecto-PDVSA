-- Esquema MySQL para sistema_pdvsa
-- Ejecutar: mysql -u root < sql/schema_mysql.sql

CREATE DATABASE IF NOT EXISTS sistema_pdvsa
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sistema_pdvsa;

-- Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    permisos_json JSON DEFAULT (JSON_OBJECT()),
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_roles_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    nombre_completo VARCHAR(200) NOT NULL,
    cargo VARCHAR(100) NULL,
    email VARCHAR(150) NULL,
    telefono_extension VARCHAR(50) NULL,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    creado_por INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sesion_activa_token VARCHAR(255) NULL,
    sesion_expira_en TIMESTAMP NULL,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id),
    CONSTRAINT fk_usuarios_creado_por FOREIGN KEY (creado_por) REFERENCES usuarios(id),
    INDEX idx_usuarios_nombre_usuario (nombre_usuario),
    INDEX idx_usuarios_rol (rol_id),
    INDEX idx_usuarios_estado (estado),
    INDEX idx_usuarios_sesion_token (sesion_activa_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración del sistema
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT NOT NULL,
    descripcion TEXT NULL,
    modificable_por VARCHAR(50) DEFAULT 'admin',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: localidades
CREATE TABLE IF NOT EXISTS localidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_localidades_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: areas
CREATE TABLE IF NOT EXISTS areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    localidad_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_areas_localidad FOREIGN KEY (localidad_id) REFERENCES localidades(id),
    INDEX idx_areas_localidad_id (localidad_id),
    INDEX idx_areas_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: instalaciones
CREATE TABLE IF NOT EXISTS instalaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo_instalacion VARCHAR(50) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_instalaciones_area FOREIGN KEY (area_id) REFERENCES areas(id),
    INDEX idx_instalaciones_area_id (area_id),
    INDEX idx_instalaciones_codigo (codigo_instalacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: zonas
CREATE TABLE IF NOT EXISTS zonas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instalacion_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo_zona VARCHAR(50) NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_zonas_instalacion FOREIGN KEY (instalacion_id) REFERENCES instalaciones(id),
    INDEX idx_zonas_instalacion_id (instalacion_id),
    INDEX idx_zonas_codigo (codigo_zona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: categorias_equipo
CREATE TABLE IF NOT EXISTS categorias_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    color_calendario VARCHAR(7) DEFAULT '#7BA7D9',
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categorias_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: grupos_seguridad
CREATE TABLE IF NOT EXISTS grupos_seguridad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_grupos_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: niveles_mantenimiento
CREATE TABLE IF NOT EXISTS niveles_mantenimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre_nivel VARCHAR(50) NOT NULL,
    frecuencia VARCHAR(50) NOT NULL,
    duracion_estimada_horas DECIMAL(4,2) NOT NULL,
    cantidad_ejecutores_requeridos INT DEFAULT 1,
    descripcion TEXT NULL,
    es_automatico BOOLEAN DEFAULT TRUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_niveles_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_equipo(id),
    INDEX idx_niveles_categoria (categoria_id),
    INDEX idx_niveles_frecuencia (frecuencia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: checklists
CREATE TABLE IF NOT EXISTS checklists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nivel_mantenimiento_id INT NOT NULL,
    nombre_checklist VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_checklists_nivel FOREIGN KEY (nivel_mantenimiento_id) REFERENCES niveles_mantenimiento(id),
    INDEX idx_checklists_nivel (nivel_mantenimiento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: checklist_items
CREATE TABLE IF NOT EXISTS checklist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT NOT NULL,
    orden_ejecucion INT NOT NULL,
    descripcion_tarea TEXT NOT NULL,
    es_obligatorio BOOLEAN DEFAULT TRUE,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_checklist_items_checklist FOREIGN KEY (checklist_id) REFERENCES checklists(id),
    INDEX idx_checklist_items_checklist (checklist_id),
    INDEX idx_checklist_items_orden (orden_ejecucion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: tipos_falla
CREATE TABLE IF NOT EXISTS tipos_falla (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipos_falla_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: prioridades_falla
CREATE TABLE IF NOT EXISTS prioridades_falla (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    color_alert VARCHAR(7) NOT NULL,
    descripcion TEXT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_prioridades_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: equipos
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_activo_fijo VARCHAR(100) NOT NULL UNIQUE,
    serial VARCHAR(100) NULL,
    nombre VARCHAR(200) NOT NULL,
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    descripcion TEXT NULL,
    categoria_id INT NOT NULL,
    zona_id INT NOT NULL,
    grupo_responsable VARCHAR(100) NULL,
    grupo_seguridad_id INT NULL,
    estado VARCHAR(50) DEFAULT 'activo',
    foto_equipo VARCHAR(255) NULL,
    fecha_registro DATE DEFAULT CURRENT_TIMESTAMP,
    registrado_por_usuario_id INT NOT NULL,
    ultima_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modificado_por_usuario_id INT NULL,
    esta_bloqueado BOOLEAN DEFAULT FALSE,
    motivo_bloqueo TEXT NULL,
    bloqueado_por_usuario_id INT NULL,
    fecha_bloqueo TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias_equipo(id),
    CONSTRAINT fk_equipos_zona FOREIGN KEY (zona_id) REFERENCES zonas(id),
    CONSTRAINT fk_equipos_grupo_seguridad FOREIGN KEY (grupo_seguridad_id) REFERENCES grupos_seguridad(id),
    CONSTRAINT fk_equipos_registrado_por FOREIGN KEY (registrado_por_usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_equipos_modificado_por FOREIGN KEY (modificado_por_usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_equipos_bloqueado_por FOREIGN KEY (bloqueado_por_usuario_id) REFERENCES usuarios(id),
    INDEX idx_equipos_numero_activo (numero_activo_fijo),
    INDEX idx_equipos_nombre (nombre),
    INDEX idx_equipos_categoria (categoria_id),
    INDEX idx_equipos_zona (zona_id),
    INDEX idx_equipos_estado (estado),
    INDEX idx_equipos_bloqueado (esta_bloqueado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: ordenes_preventivas
CREATE TABLE IF NOT EXISTS ordenes_preventivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_unico VARCHAR(50) NOT NULL UNIQUE,
    equipo_id INT NOT NULL,
    nivel_mantenimiento_id INT NOT NULL,
    fecha_planificada DATE NOT NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    fecha_asignacion DATE NULL,
    fecha_inicio_ejecucion TIMESTAMP NULL,
    fecha_cierre_ejecucion TIMESTAMP NULL,
    estado VARCHAR(50) DEFAULT 'planificada',
    planificador_id INT NOT NULL,
    supervisor_asigno_id INT NULL,
    mantenedor_id INT NULL,
    duracion_estimada_horas DECIMAL(4,2) NOT NULL,
    duracion_real_horas DECIMAL(4,2) NULL,
    observaciones_mantenedor TEXT NULL,
    observaciones_supervisor TEXT NULL,
    motivo_suspension TEXT NULL,
    descripcion TEXT NULL,
    codigo_otp_validacion VARCHAR(6) NULL,
    generada_automaticamente BOOLEAN DEFAULT TRUE,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ordenes_preventivas_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    CONSTRAINT fk_ordenes_preventivas_nivel FOREIGN KEY (nivel_mantenimiento_id) REFERENCES niveles_mantenimiento(id),
    CONSTRAINT fk_ordenes_preventivas_planificador FOREIGN KEY (planificador_id) REFERENCES usuarios(id),
    CONSTRAINT fk_ordenes_preventivas_supervisor FOREIGN KEY (supervisor_asigno_id) REFERENCES usuarios(id),
    CONSTRAINT fk_ordenes_preventivas_mantenedor FOREIGN KEY (mantenedor_id) REFERENCES usuarios(id),
    INDEX idx_otp_codigo (codigo_unico),
    INDEX idx_otp_equipo (equipo_id),
    INDEX idx_otp_estado (estado),
    INDEX idx_otp_fecha_planificada (fecha_planificada),
    INDEX idx_otp_mantenedor (mantenedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: ejecuciones_preventivas
CREATE TABLE IF NOT EXISTS ejecuciones_preventivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_preventiva_id INT NOT NULL,
    mantenedor_id INT NOT NULL,
    fecha_inicio_real TIMESTAMP NULL,
    fecha_fin_real TIMESTAMP NULL,
    observaciones_mantenedor TEXT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ejecuciones_orden_preventiva FOREIGN KEY (orden_preventiva_id) REFERENCES ordenes_preventivas(id),
    CONSTRAINT fk_ejecuciones_mantenedor FOREIGN KEY (mantenedor_id) REFERENCES usuarios(id),
    INDEX idx_ejecuciones_otp (orden_preventiva_id),
    INDEX idx_ejecuciones_mantenedor (mantenedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: ejecucion_checklist_items
CREATE TABLE IF NOT EXISTS ejecucion_checklist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ejecucion_preventiva_id INT NULL,
    orden_correctiva_id INT NULL,
    checklist_item_id INT NOT NULL,
    marcado_como_hecho BOOLEAN DEFAULT FALSE,
    observacion_item TEXT NULL,
    fecha_marcado TIMESTAMP NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_ejecucion_items_ejecucion FOREIGN KEY (ejecucion_preventiva_id) REFERENCES ejecuciones_preventivas(id) ON DELETE SET NULL,
    CONSTRAINT fk_ejecucion_items_orden_correctiva FOREIGN KEY (orden_correctiva_id) REFERENCES ordenes_correctivas(id) ON DELETE SET NULL,
    CONSTRAINT fk_ejecucion_items_checklist FOREIGN KEY (checklist_item_id) REFERENCES checklist_items(id),
    INDEX idx_ejecucion_items_ejecucion (ejecucion_preventiva_id),
    INDEX idx_ejecucion_items_otc (orden_correctiva_id),
    INDEX idx_ejecucion_items_checklist (checklist_item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: ordenes_correctivas
CREATE TABLE IF NOT EXISTS ordenes_correctivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_unico VARCHAR(50) NOT NULL UNIQUE,
    equipo_id INT NOT NULL,
    tipo_falla_id INT NOT NULL,
    prioridad_id INT NOT NULL,
    zona_id INT NULL,
    fecha_reporte DATE NOT NULL,
    hora_reporte TIME NOT NULL,
    reportado_por_usuario_id INT NOT NULL,
    fecha_inicio_reparacion DATE NULL,
    hora_inicio_reparacion TIME NULL,
    fecha_fin_reparacion DATE NULL,
    hora_fin_reparacion TIME NULL,
    descripcion_falla TEXT NOT NULL,
    acciones_tomadas TEXT NULL,
    causa_raiz TEXT NULL,
    repuestos_utilizados TEXT NULL,
    downtime_calculado_minutos INT NULL,
    estado VARCHAR(50) DEFAULT 'reportada',
    supervisor_asigno_id INT NULL,
    mantenedor_id INT NULL,
    cerrada_por_usuario_id INT NULL,
    fecha_cierre TIMESTAMP NULL,
    es_preventivo_fuera_de_plan BOOLEAN DEFAULT FALSE,
    es_reporte_condicion BOOLEAN DEFAULT FALSE,
    condicion_observada TEXT NULL,
    recomendacion_condicion TEXT NULL,
    orden_preventiva_relacionada_id INT NULL,
    orden_correctiva_relacionada_id INT NULL,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_otc_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    CONSTRAINT fk_otc_tipo_falla FOREIGN KEY (tipo_falla_id) REFERENCES tipos_falla(id),
    CONSTRAINT fk_otc_prioridad FOREIGN KEY (prioridad_id) REFERENCES prioridades_falla(id),
    CONSTRAINT fk_otc_zona FOREIGN KEY (zona_id) REFERENCES zonas(id),
    CONSTRAINT fk_otc_reportado_por FOREIGN KEY (reportado_por_usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_otc_supervisor FOREIGN KEY (supervisor_asigno_id) REFERENCES usuarios(id),
    CONSTRAINT fk_otc_mantenedor FOREIGN KEY (mantenedor_id) REFERENCES usuarios(id),
    CONSTRAINT fk_otc_cerrada_por FOREIGN KEY (cerrada_por_usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_otc_orden_preventiva_relacionada FOREIGN KEY (orden_preventiva_relacionada_id) REFERENCES ordenes_preventivas(id),
    CONSTRAINT fk_otc_orden_correctiva_relacionada FOREIGN KEY (orden_correctiva_relacionada_id) REFERENCES ordenes_correctivas(id),
    INDEX idx_otc_codigo (codigo_unico),
    INDEX idx_otc_equipo (equipo_id),
    INDEX idx_otc_estado (estado),
    INDEX idx_otc_fecha_reporte (fecha_reporte),
    INDEX idx_otc_mantenedor (mantenedor_id),
    INDEX idx_otc_es_condicion (es_reporte_condicion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: fotos_correctivas
CREATE TABLE IF NOT EXISTS fotos_correctivas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_correctiva_id INT NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    tamano_kb INT NOT NULL,
    tipo VARCHAR(20) DEFAULT 'durante',
    subida_por_usuario_id INT NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fotos_otc FOREIGN KEY (orden_correctiva_id) REFERENCES ordenes_correctivas(id),
    CONSTRAINT fk_fotos_subida_por FOREIGN KEY (subida_por_usuario_id) REFERENCES usuarios(id),
    INDEX idx_fotos_otc (orden_correctiva_id),
    INDEX idx_fotos_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: calibraciones
CREATE TABLE IF NOT EXISTS calibraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    tipo_calibracion VARCHAR(50) NOT NULL,
    fecha_ultima_calibracion DATE NOT NULL,
    fecha_proxima_calibracion DATE NOT NULL,
    entidad_certificadora VARCHAR(200) NULL,
    certificado_ruta VARCHAR(255) NULL,
    rango_medicion VARCHAR(100) NULL,
    error_permitido VARCHAR(50) NULL,
    observaciones TEXT NULL,
    registrado_por_usuario_id INT NOT NULL,
    estado VARCHAR(50) DEFAULT 'al_dia',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_calibraciones_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    CONSTRAINT fk_calibraciones_registrado_por FOREIGN KEY (registrado_por_usuario_id) REFERENCES usuarios(id),
    INDEX idx_calibraciones_equipo (equipo_id),
    INDEX idx_calibraciones_fecha_proxima (fecha_proxima_calibracion),
    INDEX idx_calibraciones_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: reportes_generados
CREATE TABLE IF NOT EXISTS reportes_generados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte VARCHAR(50) NOT NULL,
    formato VARCHAR(10) DEFAULT 'pdf',
    generado_por_usuario_id INT NOT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    parametros_filtros_json JSON NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    nombre_archivo_descarga VARCHAR(255) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reportes_usuario FOREIGN KEY (generado_por_usuario_id) REFERENCES usuarios(id),
    INDEX idx_reportes_usuario (generado_por_usuario_id),
    INDEX idx_reportes_tipo (tipo_reporte),
    INDEX idx_reportes_fecha (fecha_generacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: logs_auditoria
CREATE TABLE IF NOT EXISTS logs_auditoria (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(20) NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_afectado_id INT NULL,
    datos_anteriores_json JSON NULL,
    datos_nuevos_json JSON NULL,
    direccion_ip VARCHAR(45) NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT NULL,
    CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_logs_usuario (usuario_id),
    INDEX idx_logs_accion (accion),
    INDEX idx_logs_tabla (tabla_afectada),
    INDEX idx_logs_fecha (fecha_hora),
    INDEX idx_logs_registro (tabla_afectada, registro_afectado_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: alertas
CREATE TABLE IF NOT EXISTS alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_destino_id INT NOT NULL,
    tipo_alerta VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    entidad_relacionada_tipo VARCHAR(20) NULL,
    entidad_relacionada_id INT NULL,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento_referencia DATE NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_lectura TIMESTAMP NULL,
    accion_tomada VARCHAR(20) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alertas_usuario FOREIGN KEY (usuario_destino_id) REFERENCES usuarios(id),
    INDEX idx_alertas_usuario (usuario_destino_id),
    INDEX idx_alertas_tipo (tipo_alerta),
    INDEX idx_alertas_leida (leida),
    INDEX idx_alertas_fecha (fecha_generacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles iniciales del sistema
INSERT INTO roles (nombre, permisos_json, descripcion) VALUES
('Administrador', '{}', 'Gestión total del sistema'),
('Planificador/Programador', '{}', 'Configura frecuencias, genera calendario'),
('Supervisor', '{}', 'Asigna, audita, desbloquea, ve reportes'),
('Mantenedor', '{}', 'Ejecuta, cierra, reporta fallas')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Usuario administrador por defecto (cambiar contraseña tras el primer acceso)
-- Usuario: admin  |  Contraseña: Admin2026!
INSERT INTO usuarios (rol_id, nombre_completo, cargo, email, nombre_usuario, contrasena_hash, estado)
SELECT r.id, 'Administrador del Sistema', 'Administrador', 'admin@sistema.local', 'admin',
       '$2y$10$UBjLoQSH2RNAbXTQrDXohuIRB2u7RMqtSf./nKz4YbYDDcd5gERTy', 'activo'
FROM roles r
WHERE r.nombre = 'Administrador'
  AND NOT EXISTS (SELECT 1 FROM usuarios WHERE nombre_usuario = 'admin');
