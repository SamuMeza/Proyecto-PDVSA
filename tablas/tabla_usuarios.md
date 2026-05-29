# Tabla: `usuarios`

Registro de todos los usuarios del sistema.

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SERIAL | PK, NOT NULL | Identificador único |
| `rol_id` | INTEGER | FK → roles.id, NOT NULL | Rol del usuario |
| `nombre_completo` | VARCHAR(200) | NOT NULL | Nombre completo del empleado |
| `cargo` | VARCHAR(100) | NULL | Cargo o puesto |
| `email` | VARCHAR(150) | NULL | Correo electrónico |
| `telefono_extension` | VARCHAR(50) | NULL | Teléfono o extensión de oficina |
| `nombre_usuario` | VARCHAR(50) | NOT NULL, UNIQUE | Nombre de usuario para login |
| `contrasena_hash` | VARCHAR(255) | NOT NULL | Contraseña hasheada (bcrypt) |
| `foto_perfil` | VARCHAR(255) | NULL | Ruta a imagen de avatar |
| `estado` | VARCHAR(50) | DEFAULT 'activo' | Estado: activo / inactivo / bloqueado |
| `ultimo_acceso` | TIMESTAMP | NULL | Fecha y hora del último login |
| `creado_por` | INTEGER | FK → usuarios.id, NULL | Usuario que creó este registro |
| `fecha_creacion` | TIMESTAMP | DEFAULT NOW() | Fecha de creación |
| `sesion_activa_token` | VARCHAR(255) | NULL | Token de sesión actual (para control de inactividad) |
| `sesion_expira_en` | TIMESTAMP | NULL | Fecha de expiración del token de sesión |
| `actualizado_en` | TIMESTAMP | DEFAULT NOW() | Fecha de última modificación |

---

**Relaciones:**
- Muchos a uno con `roles`
- Auto-referencia con `usuarios` (creado_por)
- Uno a muchos con `ordenes_preventivas` (planificador, supervisor, mantenedor)
- Uno a muchos con `ordenes_correctivas` (reportado_por, supervisor, mantenedor)
- Uno a muchos con `equipos` (registrado_por, modificado_por)

**Índices:**
- `idx_usuarios_nombre_usuario` (nombre_usuario)
- `idx_usuarios_rol` (rol_id)
- `idx_usuarios_estado` (estado)
- `idx_usuarios_sesion_token` (sesion_activa_token)
