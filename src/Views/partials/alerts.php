<?php $error = $_GET['error'] ?? ''; ?>
<?php if ($error): ?>
<div class="alert alert-error">
    <?php
    $messages = [
        'sin_permiso' => 'No tiene permisos para realizar esta acción.',
        'sesion_expirada' => 'Su sesión ha expirado. Inicie sesión nuevamente.',
        'credenciales' => 'Usuario o contraseña incorrectos.',
    ];
    echo htmlspecialchars($messages[$error] ?? $error);
    ?>
</div>
<?php endif; ?>

<?php $success = $_GET['success'] ?? $_GET['actualizado'] ?? $_GET['creado'] ?? ''; ?>
<?php if ($success): ?>
<div class="alert alert-success">Operación realizada exitosamente.</div>
<?php endif; ?>
