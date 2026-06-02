<h1>Verificación OTP</h1>
<p class="auth-subtitle">Ingrese el código de verificación</p>
<?php if (!empty($codigoGenerado)): ?>
<div class="alert alert-info" style="font-size:1.5em;text-align:center;letter-spacing:4px;font-weight:bold;">
    Código de prueba: <?= htmlspecialchars($codigoGenerado) ?>
</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post" class="auth-form">
    <label for="otp_code">Código OTP</label>
    <input type="text" id="otp_code" name="otp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus placeholder="000000">
    <button type="submit" class="btn btn-primary">Verificar</button>
</form>
<p class="auth-footer"><a href="<?= \App\Core\App::BASE_PATH ?>/login">Volver al inicio de sesión</a></p>
</div>
<script src="<?= \App\Core\App::BASE_PATH ?>/public/assets/js/theme.js"></script>
</body>
</html>
