<div class="page-header">
    <h1 class="page-title">Crear Usuario</h1>
</div>

<div class="page-card">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/usuarios/crear" class="form-grid">
        <div class="form-group">
            <label>Nombre Completo *</label>
            <input type="text" name="nombre_completo" value="<?= htmlspecialchars($_POST['nombre_completo'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Nombre de Usuario *</label>
            <input type="text" name="nombre_usuario" value="<?= htmlspecialchars($_POST['nombre_usuario'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Contraseña *</label>
            <input type="password" name="contrasena" required minlength="6">
        </div>
        <div class="form-group">
            <label>Rol *</label>
            <select name="rol_id" required>
                <option value="">Seleccionar rol...</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ((int)($_POST['rol_id'] ?? 0) === (int)$r['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Cargo</label>
            <input type="text" name="cargo" value="<?= htmlspecialchars($_POST['cargo'] ?? '') ?>">
        </div>
        <div class="form-group" style="grid-column:1 / -1;">
            <label>Teléfono / Extensión</label>
            <input type="text" name="telefono_extension" placeholder="+584161234567" value="<?= htmlspecialchars($_POST['telefono_extension'] ?? '') ?>">
        </div>
        <div class="form-group" style="grid-column:1 / -1; text-align:right;">
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="<?= \App\Core\App::BASE_PATH ?>/usuarios" class="btn btn-outline">Cancelar</a>
            </div>
        </div>
    </form>
</div>
