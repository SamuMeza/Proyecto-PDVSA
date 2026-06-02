                <div class="page-header">
                    <h1 class="page-title">Ordenes correctivas</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

<?php if ($ordenDetalle): ?>
                <div class="page-card">
                    <div class="page-header" style="margin-bottom:1rem;">
                        <h2>Detalle: <?= htmlspecialchars($ordenDetalle['codigo_unico']) ?></h2>
                        <a href="<?= \App\Core\App::BASE_PATH ?>/correctivas" class="btn btn-outline">&larr; Volver</a>
                    </div>
                    <div class="form-grid" style="grid-template-columns:1fr 1fr;">
                        <div><strong>Equipo:</strong> <?= htmlspecialchars($ordenDetalle['equipo_nombre'] ?? '') ?> (<?= htmlspecialchars($ordenDetalle['numero_activo_fijo'] ?? '') ?>)</div>
                        <div><strong>Tipo de falla:</strong> <?= htmlspecialchars($ordenDetalle['tipo_falla_nombre'] ?? '') ?></div>
                        <div><strong>Prioridad:</strong> <span style="color:<?= htmlspecialchars($ordenDetalle['prioridad_color'] ?? '#333') ?>"><?= htmlspecialchars($ordenDetalle['prioridad_nombre'] ?? '') ?></span></div>
                        <div><strong>Zona:</strong> <?= htmlspecialchars($ordenDetalle['zona_nombre'] ?? 'N/A') ?></div>
                        <div><strong>Reportado por:</strong> <?= htmlspecialchars($ordenDetalle['reportado_por_nombre'] ?? '') ?></div>
                        <div><strong>Fecha/Hora reporte:</strong> <?= htmlspecialchars($ordenDetalle['fecha_reporte'] ?? '') ?> <?= htmlspecialchars($ordenDetalle['hora_reporte'] ?? '') ?></div>
                        <div><strong>Estado:</strong> <span class="tag tag-<?= htmlspecialchars($ordenDetalle['estado']) ?>"><?= htmlspecialchars(ucfirst($ordenDetalle['estado'])) ?></span></div>
                        <div><strong>Supervisor:</strong> <?= htmlspecialchars($ordenDetalle['supervisor_nombre'] ?? 'Sin asignar') ?></div>
                        <div><strong>Mantenedor:</strong> <?= htmlspecialchars($ordenDetalle['mantenedor_nombre'] ?? 'Sin asignar') ?></div>
                        <?php if ($ordenDetalle['fecha_inicio_reparacion']): ?>
                        <div><strong>Inicio reparacion:</strong> <?= htmlspecialchars($ordenDetalle['fecha_inicio_reparacion']) ?> <?= htmlspecialchars($ordenDetalle['hora_inicio_reparacion'] ?? '') ?></div>
                        <?php endif; ?>
                        <?php if ($ordenDetalle['fecha_fin_reparacion']): ?>
                        <div><strong>Fin reparacion:</strong> <?= htmlspecialchars($ordenDetalle['fecha_fin_reparacion']) ?> <?= htmlspecialchars($ordenDetalle['hora_fin_reparacion'] ?? '') ?></div>
                        <?php endif; ?>
                        <?php if ($ordenDetalle['downtime_calculado_minutos']): ?>
                        <div><strong>Downtime:</strong> <?= htmlspecialchars($ordenDetalle['downtime_calculado_minutos']) ?> min</div>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top:1rem;">
                        <h3>Descripcion de la falla</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['descripcion_falla'])) ?></p>
                    </div>
                    <?php if ($ordenDetalle['acciones_tomadas']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Acciones tomadas</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['acciones_tomadas'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($ordenDetalle['causa_raiz']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Causa raiz</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['causa_raiz'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($ordenDetalle['repuestos_utilizados']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Repuestos utilizados</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['repuestos_utilizados'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($transicionesMap[$ordenDetalle['estado']]) && !empty($transicionesMap[$ordenDetalle['estado']]) && $puedeCambiarEstado): ?>
                    <div style="margin-top:1.5rem; border-top:1px solid var(--border); padding-top:1rem;">
                        <h3>Cambiar estado</h3>
                        <form method="post" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                            <input type="hidden" name="cambiar_estado" value="1">
                            <input type="hidden" name="ot_id" value="<?= $ordenDetalle['id'] ?>">
                            <select name="nuevo_estado" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($transicionesMap[$ordenDetalle['estado']] as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado) ?>"><?= htmlspecialchars(ucfirst($estado)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Cambiar estado a la orden?')">Cambiar</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="page-card">
                    <h3>Fotos (<?= count($fotos) ?>/3)</h3>
                    <?php if ($puedeCrear || $puedeEditar): ?>
                    <?php if (count($fotos) < 3): ?>
                    <form method="post" enctype="multipart/form-data" style="margin-bottom:1rem;">
                        <input type="hidden" name="subir_foto" value="1">
                        <input type="hidden" name="ot_id" value="<?= $ordenDetalle['id'] ?>">
                        <div class="form-group">
                            <input type="file" name="foto" accept=".jpg,.jpeg,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Subir foto</button>
                    </form>
                    <?php else: ?>
                    <p><em>Limite de 3 fotos alcanzado.</em></p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                        <?php foreach ($fotos as $foto): ?>
                            <div style="position:relative; border:1px solid var(--border); border-radius:8px; overflow:hidden; max-width:200px;">
                                <a href="<?= \App\Core\App::BASE_PATH ?>/public/assets/uploads/fotos-fallas/<?= htmlspecialchars($foto['ruta_archivo']) ?>" target="_blank">
                                    <img src="<?= \App\Core\App::BASE_PATH ?>/public/assets/uploads/fotos-fallas/<?= htmlspecialchars($foto['ruta_archivo']) ?>" alt="Foto" style="width:100%; display:block;">
                                </a>
                                <div style="padding:4px 8px; font-size:0.85em;">
                                    <?= htmlspecialchars($foto['nombre_original'] ?? '') ?> (<?= $foto['tamano_kb'] ?> KB)
                                    <?php if ($puedeEliminar): ?>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Eliminar esta foto?')">
                                        <input type="hidden" name="eliminar_foto" value="1">
                                        <input type="hidden" name="foto_id" value="<?= $foto['id'] ?>">
                                        <button type="submit" style="background:none;border:none;color:var(--danger);cursor:pointer;text-decoration:underline;">Eliminar</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($fotos)): ?>
                            <p>No hay fotos asociadas.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="page-card">
                    <h3>Checklist de ejecucion</h3>
                    <?php if (!empty($checklistEjecuciones)): ?>
                    <table class="data-table">
                        <thead><tr>
                            <th>Item</th><th>Requerido</th><th>Estado</th><th>Fecha</th>
                            <?php if ($puedeEditar): ?><th>Accion</th><?php endif; ?>
                        </tr></thead>
                        <tbody>
                            <?php foreach ($checklistEjecuciones as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_descripcion'] ?? 'Item #' . $item['checklist_item_id']) ?></td>
                                <td><?= $item['es_requerido'] ? 'Si' : 'No' ?></td>
                                <td><?= $item['marcado_como_hecho'] ? 'Hecho' : 'Pendiente' ?></td>
                                <td><?= $item['fecha_marcado'] ?? '—' ?></td>
                                <?php if ($puedeEditar): ?>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="toggle_checklist_item" value="1">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="view_id" value="<?= $ordenDetalle['id'] ?>">
                                        <button type="submit" class="btn btn-outline small-action"><?= $item['marcado_como_hecho'] ? 'Desmarcar' : 'Marcar' ?></button>
                                    </form>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No hay checklist asociado a esta orden.</p>
                    <?php endif; ?>
                </div>

                <div class="page-card">
                    <h3>Historial de auditoria</h3>
                    <?php if (!empty($auditorias)): ?>
                    <table class="data-table">
                        <thead><tr><th>Fecha</th><th>Usuario</th><th>Accion</th><th>Descripcion</th></tr></thead>
                        <tbody>
                            <?php foreach ($auditorias as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['fecha_hora']) ?></td>
                                <td><?= htmlspecialchars($log['usuario_nombre'] ?? 'Sistema') ?></td>
                                <td><span class="tag tag-<?= htmlspecialchars($log['accion']) ?>"><?= htmlspecialchars($log['accion']) ?></span></td>
                                <td><?= htmlspecialchars($log['descripcion'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No hay eventos registrados.</p>
                    <?php endif; ?>
                </div>
<?php else: ?>
                <div class="page-card">
                    <form method="get" class="filter-panel" aria-label="Filtrar ordenes correctivas">
                        <div class="form-group">
                            <label for="filter_tipo_falla">Tipo de falla</label>
                            <select id="filter_tipo_falla" name="filter_tipo_falla">
                                <option value="0">Todas</option>
                                <?php foreach ($tiposFalla as $tf): ?>
                                    <option value="<?= $tf['id'] ?>" <?= $filterTipoFalla === (int) $tf['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_prioridad">Prioridad</label>
                            <select id="filter_prioridad" name="filter_prioridad">
                                <option value="0">Todas</option>
                                <?php foreach ($prioridades as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $filterPrioridad === (int) $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_zona">Zona</label>
                            <select id="filter_zona" name="filter_zona">
                                <option value="0">Todas</option>
                                <?php foreach ($zonas as $z): ?>
                                    <option value="<?= $z['id'] ?>" <?= $filterZona === (int) $z['id'] ? 'selected' : '' ?>><?= htmlspecialchars($z['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            <select id="filter_estado" name="filter_estado">
                                <option value="todos" <?= $filterEstado === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="reportada" <?= $filterEstado === 'reportada' ? 'selected' : '' ?>>Reportada</option>
                                <option value="en_progreso" <?= $filterEstado === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                                <option value="completada" <?= $filterEstado === 'completada' ? 'selected' : '' ?>>Completada</option>
                                <option value="cerrada" <?= $filterEstado === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                                <option value="cancelada" <?= $filterEstado === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_equipo">Equipo</label>
                            <select id="filter_equipo" name="filter_equipo">
                                <option value="0">Todos</option>
                                <?php $equiposList = \App\Models\Equipo::allActive(); ?>
                                <?php foreach ($equiposList as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $filterEquipo === (int) $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>

                    <?php if ($puedeCrear): ?>
                        <p><a href="#formulario-correctiva" class="btn btn-primary">Crear nueva orden correctiva</a></p>
                    <?php endif; ?>

                    <table class="data-table">
                        <thead><tr>
                            <th>Codigo</th><th>Equipo</th><th>Tipo de falla</th><th>Prioridad</th><th>Zona</th><th>Fecha</th><th>Estado</th><th>Acciones</th>
                        </tr></thead>
                        <tbody>
                            <?php if (empty($ordenes)): ?>
                                <tr><td colspan="8">No se encontraron ordenes correctivas con los filtros seleccionados.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($orden['codigo_unico']) ?></strong></td>
                                    <td><?= htmlspecialchars($orden['equipo_nombre'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($orden['tipo_falla'] ?? '') ?></td>
                                    <td style="color:<?= htmlspecialchars($orden['prioridad_color'] ?? '#333') ?>"><?= htmlspecialchars($orden['prioridad'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($orden['zona'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($orden['fecha_reporte']) ?><br><small><?= htmlspecialchars($orden['hora_reporte']) ?></small></td>
                                    <td><span class="tag tag-<?= htmlspecialchars($orden['estado']) ?>"><?= htmlspecialchars(ucfirst($orden['estado'])) ?></span></td>
                                    <td>
                                        <a href="<?= \App\Core\App::BASE_PATH ?>/correctivas?view=<?= $orden['id'] ?>" class="btn btn-outline small-action">Ver</a>
                                        <?php if ($puedeEditar && $orden['estado'] !== 'cerrada'): ?>
                                            <a href="<?= \App\Core\App::BASE_PATH ?>/correctivas?edit=<?= $orden['id'] ?>" class="btn btn-outline small-action">Editar</a>
                                        <?php endif; ?>
                                        <?php if ($puedeEliminar): ?>
                                            <form method="post" style="display:inline-block;" onsubmit="return confirm('Eliminar permanentemente la orden?');">
                                                <input type="hidden" name="eliminar_correctiva" value="<?= $orden['id'] ?>">
                                                <button type="submit" class="btn btn-outline small-action">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="page-card" id="formulario-correctiva">
                    <h2><?= $formData['id'] ? 'Editar orden correctiva' : 'Crear orden correctiva' ?></h2>
                    <form method="post" class="form-grid">
                        <input type="hidden" name="save_correctiva" value="1">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($formData['id']) ?>">

                        <div class="form-group">
                            <label for="equipo_id">Equipo</label>
                            <select id="equipo_id" name="equipo_id" required>
                                <option value="">Seleccione</option>
                                <?php $equiposForm = \App\Models\Equipo::allActive(); ?>
                                <?php foreach ($equiposForm as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $formData['equipo_id'] == $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['numero_activo_fijo'] ?? '') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo_falla_id">Tipo de falla</label>
                            <select id="tipo_falla_id" name="tipo_falla_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($tiposFalla as $tf): ?>
                                    <option value="<?= $tf['id'] ?>" <?= $formData['tipo_falla_id'] == $tf['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prioridad_id">Prioridad</label>
                            <select id="prioridad_id" name="prioridad_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($prioridades as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $formData['prioridad_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zona_id">Zona</label>
                            <select id="zona_id" name="zona_id">
                                <option value="">Seleccione</option>
                                <?php foreach ($zonas as $z): ?>
                                    <option value="<?= $z['id'] ?>" <?= $formData['zona_id'] == $z['id'] ? 'selected' : '' ?>><?= htmlspecialchars($z['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="supervisor_asigno_id">Supervisor asignado</label>
                            <select id="supervisor_asigno_id" name="supervisor_asigno_id">
                                <option value="">Sin asignar</option>
                                <?php foreach ($usuariosActivos as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $formData['supervisor_asigno_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mantenedor_id">Mantenedor asignado</label>
                            <select id="mantenedor_id" name="mantenedor_id">
                                <option value="">Sin asignar</option>
                                <?php foreach ($usuariosActivos as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $formData['mantenedor_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="checklist_id">Checklist asociado (solo al crear)</label>
                            <select id="checklist_id" name="checklist_id" <?= $formData['id'] ? 'disabled' : '' ?>>
                                <option value="">Ninguno</option>
                                <?php foreach ($checklists as $cl): ?>
                                    <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nombre_checklist']) ?> (<?= htmlspecialchars($cl['nivel'] ?? '') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($formData['id']): ?>
                                <input type="hidden" name="checklist_id" value="">
                            <?php endif; ?>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="descripcion_falla">Descripcion de la falla</label>
                            <textarea id="descripcion_falla" name="descripcion_falla" rows="4" required><?= htmlspecialchars($formData['descripcion_falla']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="acciones_tomadas">Acciones tomadas</label>
                            <textarea id="acciones_tomadas" name="acciones_tomadas" rows="3"><?= htmlspecialchars($formData['acciones_tomadas']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="causa_raiz">Causa raiz</label>
                            <textarea id="causa_raiz" name="causa_raiz" rows="2"><?= htmlspecialchars($formData['causa_raiz']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="repuestos_utilizados">Repuestos utilizados</label>
                            <textarea id="repuestos_utilizados" name="repuestos_utilizados" rows="2"><?= htmlspecialchars($formData['repuestos_utilizados']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary"><?= $formData['id'] ? 'Guardar cambios' : 'Crear orden correctiva' ?></button>
                        </div>
                    </form>
                </div>
<?php endif; ?>
