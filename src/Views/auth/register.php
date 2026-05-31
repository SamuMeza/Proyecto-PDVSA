                <div class="page-header">
                    <h1 class="page-title">Registrar usuario</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

                <div class="page-card">
                    <form method="post" class="form-grid">
                        <div class="form-group">
                            <label for="nombre_completo">Nombre completo</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre_usuario">Usuario</label>
                            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
                        </div>
                        <div class="form-group">
                            <label for="contrasena">Contraseña</label>
                            <input type="password" id="contrasena" name="contrasena" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="rol_id">Rol</label>
                            <select id="rol_id" name="rol_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cargo">Cargo</label>
                            <input type="text" id="cargo" name="cargo">
                        </div>
                        <div class="form-group">
                            <label for="telefono_extension">Teléfono / Extensión</label>
                            <input type="text" id="telefono_extension" name="telefono_extension" placeholder="+584141234567">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary">Crear usuario</button>
                        </div>
                    </form>
                </div>
<?php require dirname(__DIR__, 3) . '/public/includes/layout_footer.php'; ?>
