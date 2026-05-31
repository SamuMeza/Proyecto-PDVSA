<h1>Iniciar sesión</h1>
<p class="auth-subtitle">Sistema de Mantenimiento PDVSA</p>
<?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post" class="auth-form">
    <label for="username">Usuario</label>
    <input type="text" id="username" name="username" required autofocus>
    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password" required>
    <button type="submit" class="btn btn-primary">Ingresar</button>
</form>
<p class="auth-footer">PDVSA — Petróleos de Venezuela, S.A.</p>
</div>
<script src="<?= \App\Core\App::BASE_PATH ?>/public/assets/js/theme.js"></script>
</body>
</html>
