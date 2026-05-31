<h2>Crear Usuario</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/public/usuarios.php?action=store">
    <div class="form-group">
        <label>Nombre Completo *</label>
        <input type="text" name="nombre_completo" required>
    </div>
    <div class="form-group">
        <label>Nombre de Usuario *</label>
        <input type="text" name="nombre_usuario" required>
    </div>
    <div class="form-group">
        <label>Contraseña *</label>
        <input type="password" name="contrasena" required minlength="6">
    </div>
    <div class="form-group">
        <label>Rol *</label>
        <select name="rol_id" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Cargo</label>
        <input type="text" name="cargo">
    </div>
    <div class="form-group">
        <label>Teléfono / Extensión</label>
        <input type="text" name="telefono_extension" placeholder="+584161234567">
    </div>
    <button type="submit" class="btn btn-primary">Crear</button>
</form>
