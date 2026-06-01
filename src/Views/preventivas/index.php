                <div class="page-header">
                    <h1 class="page-title">Órdenes Preventivas</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

<?php if ($viewDetalle): ?>
                <div class="page-card">
                    <div class="page-header" style="margin-bottom:1rem;">
                        <h2>Detalle: <?= htmlspecialchars($viewDetalle['codigo_unico']) ?></h2>
                        <a href="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php" class="btn btn-outline">&larr; Volver</a>
                    </div>
                    <div class="form-grid" style="grid-template-columns:1fr 1fr;">
                        <div><strong>Equipo:</strong> <?= htmlspecialchars($viewDetalle['equipo_nombre'] ?? '') ?> (<?= htmlspecialchars($viewDetalle['numero_activo_fijo'] ?? '') ?>)</div>
                        <div><strong>Nivel:</strong> <?= htmlspecialchars($viewDetalle['nivel_nombre'] ?? 'N/A') ?></div>
                        <div><strong>Fecha planificada:</strong> <?= htmlspecialchars($viewDetalle['fecha_planificada']) ?></div>
                        <div><strong>Horario:</strong> <?= $viewDetalle['hora_inicio'] ? htmlspecialchars(substr($viewDetalle['hora_inicio'], 0, 5)) . ' - ' . htmlspecialchars(substr($viewDetalle['hora_fin'] ?? '', 0, 5)) : htmlspecialchars($viewDetalle['duracion_estimada_horas']) . ' h' ?></div>
                        <div><strong>Estado:</strong> <span class="tag tag-<?= htmlspecialchars($viewDetalle['estado']) ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $viewDetalle['estado']))) ?></span></div>
                        <div><strong>Planificador:</strong> <?= htmlspecialchars($viewDetalle['planificador_nombre'] ?? 'N/A') ?></div>
                        <div><strong>Supervisor:</strong> <?= htmlspecialchars($viewDetalle['supervisor_nombre'] ?? 'Sin asignar') ?></div>
                        <div><strong>Mantenedor:</strong> <?= htmlspecialchars($viewDetalle['mantenedor_nombre'] ?? 'Sin asignar') ?></div>
                        <?php if ($viewDetalle['fecha_inicio_ejecucion']): ?>
                        <div><strong>Inicio ejecución:</strong> <?= htmlspecialchars($viewDetalle['fecha_inicio_ejecucion']) ?></div>
                        <?php endif; ?>
                        <?php if ($viewDetalle['fecha_cierre_ejecucion']): ?>
                        <div><strong>Cierre ejecución:</strong> <?= htmlspecialchars($viewDetalle['fecha_cierre_ejecucion']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($viewDetalle['descripcion']): ?>
                    <div style="margin-top:1rem;"><h3>Descripción</h3><p><?= nl2br(htmlspecialchars($viewDetalle['descripcion'])) ?></p></div>
                    <?php endif; ?>
                    <?php if ($viewDetalle['observaciones_mantenedor']): ?>
                    <div style="margin-top:0.5rem;"><h3>Observaciones del mantenedor</h3><p><?= nl2br(htmlspecialchars($viewDetalle['observaciones_mantenedor'])) ?></p></div>
                    <?php endif; ?>
                    <?php if ($viewDetalle['observaciones_supervisor']): ?>
                    <div style="margin-top:0.5rem;"><h3>Observaciones del supervisor</h3><p><?= nl2br(htmlspecialchars($viewDetalle['observaciones_supervisor'])) ?></p></div>
                    <?php endif; ?>
                    <?php if ($viewDetalle['motivo_suspension']): ?>
                    <div style="margin-top:0.5rem;"><h3>Motivo de suspensión</h3><p><?= nl2br(htmlspecialchars($viewDetalle['motivo_suspension'])) ?></p></div>
                    <?php endif; ?>
                </div>
<?php else: ?>
                <div class="page-card">
                    <form method="get" class="filter-panel" aria-label="Filtrar preventivas">
                        <div class="form-group">
                            <label for="filter_equipo">Equipo</label>
                            <select id="filter_equipo" name="filter_equipo">
                                <option value="0">Todos</option>
                                <?php foreach ($equipos as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= ($_GET['filter_equipo'] ?? 0) == $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre'] . ' (' . ($eq['numero_activo_fijo'] ?? '') . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            <select id="filter_estado" name="filter_estado">
                                <option value="todos" <?= ($_GET['filter_estado'] ?? 'todos') === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="planificada" <?= ($_GET['filter_estado'] ?? '') === 'planificada' ? 'selected' : '' ?>>Planificada</option>
                                <option value="en_curso" <?= ($_GET['filter_estado'] ?? '') === 'en_curso' ? 'selected' : '' ?>>En curso</option>
                                <option value="cerrada" <?= ($_GET['filter_estado'] ?? '') === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                                <option value="suspendida" <?= ($_GET['filter_estado'] ?? '') === 'suspendida' ? 'selected' : '' ?>>Suspendida</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_fecha_desde">Fecha desde</label>
                            <input type="date" id="filter_fecha_desde" name="filter_fecha_desde" value="<?= htmlspecialchars($_GET['filter_fecha_desde'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="filter_fecha_hasta">Fecha hasta</label>
                            <input type="date" id="filter_fecha_hasta" name="filter_fecha_hasta" value="<?= htmlspecialchars($_GET['filter_fecha_hasta'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>

                    <?php if ($puedeCrear): ?>
                        <p><a href="#formulario-preventiva" class="btn btn-primary">Crear nueva orden preventiva</a></p>
                    <?php endif; ?>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Equipo</th>
                                <th>Fecha planificada</th>
                                <th>Horario</th>
                                <th>Estado</th>
                                <th>Planificador</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ordenes)): ?>
                                <tr><td colspan="7">No se encontraron órdenes preventivas.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($ordenes as $orden): ?>
                            <tr class="<?= $orden['estado'] === 'cerrada' ? 'inactive-row' : '' ?>">
                                <td><strong><?= htmlspecialchars($orden['codigo_unico']) ?></strong></td>
                                <td><?= htmlspecialchars($orden['equipo_nombre'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($orden['fecha_planificada']) ?></td>
                                <td><?= $orden['hora_inicio'] ? htmlspecialchars(substr($orden['hora_inicio'], 0, 5)) . ' - ' . htmlspecialchars(substr($orden['hora_fin'] ?? '', 0, 5)) : htmlspecialchars($orden['duracion_estimada_horas']) . ' h' ?></td>
                                <td><span class="tag tag-<?= $orden['estado'] ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $orden['estado']))) ?></span></td>
                                <td><?= htmlspecialchars($orden['planificador_nombre'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php?view=<?= $orden['id'] ?>" class="btn btn-outline small-action">Ver</a>
                                    <?php if ($puedeEditar && $orden['estado'] === 'planificada'): ?>
                                        <a href="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php?edit=<?= $orden['id'] ?>" class="btn btn-outline small-action">Editar</a>
                                    <?php endif; ?>
                                    <?php if ($puedeCambiarEstado): ?>
                                        <?php $transiciones = \App\Models\OrdenPreventiva::allowedTransitions($orden['estado']); ?>
                                        <?php if (!empty($transiciones)): ?>
                                        <form method="post" style="display:inline-block;" class="form-inline">
                                            <input type="hidden" name="cambiar_estado" value="<?= $orden['id'] ?>">
                                            <select name="nuevo_estado" required style="font-size:0.85rem;padding:2px 4px;">
                                                <option value="">Cambiar a...</option>
                                                <?php foreach ($transiciones as $t): ?>
                                                    <option value="<?= $t ?>"><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (in_array($orden['estado'], ['planificada', 'en_curso']) && in_array($orden['estado'] === 'planificada' ? 'en_curso' : 'cerrada', $transiciones, true)): ?>
                                                <input type="text" name="codigo_otp" placeholder="OTP" style="font-size:0.85rem;padding:2px 4px;width:80px;">
                                            <?php endif; ?>
                                            <button type="submit" class="btn btn-outline small-action">→</button>
                                        </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="page-card" id="formulario-preventiva">
                    <h2><?= $formData['id'] ? 'Editar orden preventiva' : 'Crear orden preventiva' ?></h2>
                    <form method="post" class="form-grid">
                        <input type="hidden" name="save_preventiva" value="1">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($formData['id']) ?>">
                        <input type="hidden" name="codigo_unico" value="<?= htmlspecialchars($formData['codigo_unico'] ?: \App\Helpers\SecurityHelper::generateCode('PREV-')) ?>">
                        <div class="form-group">
                            <label for="equipo_id">Equipo</label>
                            <select id="equipo_id" name="equipo_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($equipos as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $formData['equipo_id'] == $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre'] . ' (' . ($eq['numero_activo_fijo'] ?? '') . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nivel_mantenimiento_id">Nivel de mantenimiento</label>
                            <select id="nivel_mantenimiento_id" name="nivel_mantenimiento_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($nivelesMantenimiento as $nm): ?>
                                    <option value="<?= $nm['id'] ?>" <?= $formData['nivel_mantenimiento_id'] == $nm['id'] ? 'selected' : '' ?>><?= htmlspecialchars($nm['nombre_nivel']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fecha_planificada">Fecha planificada</label>
                            <input type="date" id="fecha_planificada" name="fecha_planificada" value="<?= htmlspecialchars($formData['fecha_planificada']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="hora_inicio">Hora inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" value="<?= htmlspecialchars($formData['hora_inicio']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="hora_fin">Hora fin</label>
                            <input type="time" id="hora_fin" name="hora_fin" value="<?= htmlspecialchars($formData['hora_fin']) ?>">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($formData['descripcion']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="duracion_estimada_horas">Duración (horas)</label>
                            <input type="number" id="duracion_estimada_horas" name="duracion_estimada_horas" step="0.5" min="0.5" value="<?= htmlspecialchars($formData['duracion_estimada_horas'] ?: '1') ?>">
                        </div>
                        <div class="form-group">
                            <label for="planificador_id">Planificador responsable</label>
                            <select id="planificador_id" name="planificador_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($planificadores as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $formData['planificador_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary"><?= $formData['id'] ? 'Guardar cambios' : 'Crear orden preventiva' ?></button>
                        </div>
                    </form>
                </div>
<?php endif; ?>

