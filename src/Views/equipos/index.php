                <div class="page-header">
                    <h1 class="page-title">Equipos</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

                <div class="page-card">
                    <form method="get" class="filter-panel" aria-label="Filtrar equipos">
                        <div class="form-group">
                            <label for="filter_family">Familia</label>
                            <select id="filter_family" name="filter_family">
                                <option value="">Todas</option>
                                <?php foreach ($familias as $familia): ?>
                                    <option value="<?= htmlspecialchars($familia) ?>" <?= ($_GET['filter_family'] ?? '') === $familia ? 'selected' : '' ?>><?= htmlspecialchars($familia) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_categoria">Categoría</label>
                            <select id="filter_categoria" name="filter_categoria">
                                <option value="0">Todas</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>" <?= ($_GET['filter_categoria'] ?? 0) == $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_zona">Zona</label>
                            <select id="filter_zona" name="filter_zona">
                                <option value="0">Todas</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?= $zona['id'] ?>" <?= ($_GET['filter_zona'] ?? 0) == $zona['id'] ? 'selected' : '' ?>><?= htmlspecialchars($zona['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            <select id="filter_estado" name="filter_estado">
                                <option value="todos" <?= ($_GET['filter_estado'] ?? 'todos') === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="activo" <?= ($_GET['filter_estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= ($_GET['filter_estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>

                    <?php if ($puedeCrear): ?>
                        <p><a href="#formulario-equipo" class="btn btn-primary">Crear nuevo equipo</a></p>
                    <?php endif; ?>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Familia</th>
                                <th>Categoría</th>
                                <th>Zona</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($equipos)): ?>
                                <tr><td colspan="6">No se encontraron equipos con los filtros seleccionados.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($equipos as $equipo): ?>
                                <tr class="<?= $equipo['estado'] === 'inactivo' ? 'inactive-row' : '' ?>">
                                    <td><?= htmlspecialchars($equipo['nombre']) ?></td>
                                    <td><?= htmlspecialchars($equipo['familia'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($equipo['categoria'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($equipo['zona'] ?? 'N/A') ?></td>
                                    <td><span class="tag <?= $equipo['estado'] === 'activo' ? 'tag-active' : 'tag-inactive' ?>"><?= htmlspecialchars(ucfirst($equipo['estado'])) ?></span></td>
                                    <td>
                                        <?php if ($puedeEditar): ?>
                                            <a href="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php?edit=<?= $equipo['id'] ?>" class="btn btn-outline small-action">Editar</a>
                                        <?php endif; ?>
                                        <?php if ($puedeDesactivar): ?>
                                            <form method="post" style="display:inline-block;" onsubmit="return confirm('¿Desea cambiar el estado de este equipo?');">
                                                <input type="hidden" name="toggle_estado" value="<?= $equipo['id'] ?>">
                                                <button type="submit" class="btn btn-outline"><?= $equipo['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="page-card" id="formulario-equipo">
                    <h2><?= $formData['equipo_id'] ? 'Editar equipo' : 'Crear equipo' ?></h2>
                    <form method="post" class="form-grid">
                        <input type="hidden" name="save_equipo" value="1">
                        <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($formData['equipo_id']) ?>">
                        <div class="form-group">
                            <label for="nombre">Nombre del equipo</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($formData['nombre']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="familia">Familia</label>
                            <input type="text" id="familia" name="familia" value="<?= htmlspecialchars($formData['familia']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="categoria_id">Categoría</label>
                            <select id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>" <?= $formData['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zona_id">Zona</label>
                            <select id="zona_id" name="zona_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?= $zona['id'] ?>" <?= $formData['zona_id'] == $zona['id'] ? 'selected' : '' ?>><?= htmlspecialchars($zona['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" <?= \App\Services\AuthService::isAdmin() ? '' : 'disabled' ?>>
                                <option value="activo" <?= $formData['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $formData['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                            <?php if (!\App\Services\AuthService::isAdmin()): ?>
                                <input type="hidden" name="estado" value="<?= htmlspecialchars($formData['estado']) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($formData['descripcion']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary"><?= $formData['equipo_id'] ? 'Guardar cambios' : 'Crear equipo' ?></button>
                        </div>
                    </form>
                </div>
<?php require dirname(__DIR__, 3) . '/public/includes/layout_footer.php'; ?>
