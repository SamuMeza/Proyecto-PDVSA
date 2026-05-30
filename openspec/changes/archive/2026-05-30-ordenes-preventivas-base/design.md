## Diseño del módulo de órdenes preventivas

### Modelo de datos

`ordenes_preventivas` debe incluir al menos:
- `id`
- `equipo_id`
- `categoria_id`
- `fecha_programada`
- `hora_inicio`
- `hora_fin`
- `estado` (`Planificada`, `En curso`, `Cerrada`, `Suspendida`)
- `otp_codigo`
- `otp_generado_en`
- `otp_expira_en`
- `creado_por`
- `actualizado_en`

### Flujo de trabajo

1. El Planificador crea o edita una OT preventiva.
2. El bloque se muestra en el calendario de mantenimiento.
3. Cuando la OT se ejecuta, se genera un OTP para validar el trabajo.
4. El técnico ingresa el OTP para confirmar la intervención.
5. El estado avanza a `En curso`, `Cerrada` o `Suspendida` según el proceso.

### Permisos

- Admin/Supervisor: crear y editar bloques en calendario.
- Planificador: editar bloques existentes y crear nuevas OT/solicitudes.
- Mantenedor: solo ver.

### OTP

El OTP es un código de orden de trabajo que valida que la intervención corresponde a la OT preventiva.

### Interacción con el calendario

Las OT preventivas se visualizan como bloques horarios en la vista semanal/mensual y se administran desde el calendario.
