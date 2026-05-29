<?php
require_once __DIR__ . '/auth_functions.php';

requerirAdministrador();

$error = '';
$exito = '';
$roles = rolesDisponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = crearUsuario([
        'rol_id'              => $_POST['rol_id'] ?? '',
        'nombre_completo'     => $_POST['nombre_completo'] ?? '',
        'cargo'               => $_POST['cargo'] ?? '',
        'email'               => $_POST['email'] ?? '',
        'telefono_extension'  => $_POST['telefono_extension'] ?? '',
        'nombre_usuario'      => $_POST['nombre_usuario'] ?? '',
        'contrasena'          => $_POST['contrasena'] ?? '',
        'creado_por'          => $_SESSION['usuario_id'],
    ]);

    if ($resultado['ok']) {
        $exito = 'Usuario registrado correctamente.';
    } else {
        $error = $resultado['error'];
    }
}

$pageTitle = 'Registrar usuario';
$pageSlug  = 'registrar';
require dirname(__DIR__) . '/public/includes/layout.php';
?>
                <h1 class="page-title">Registrar usuario</h1>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                    Solo el rol <strong>Administrador</strong> puede crear cuentas en el sistema.
                </p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($exito): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($exito) ?></div>
                <?php endif; ?>

                <div class="page-card">
                    <form method="post" class="auth-form" style="max-width: 560px;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre_completo">Nombre completo *</label>
                                <input type="text" id="nombre_completo" name="nombre_completo" required
                                       value="<?= htmlspecialchars($_POST['nombre_completo'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="nombre_usuario">Usuario *</label>
                                <input type="text" id="nombre_usuario" name="nombre_usuario" required
                                       value="<?= htmlspecialchars($_POST['nombre_usuario'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="rol_id">Rol *</label>
                                <select id="rol_id" name="rol_id" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= (int) $rol['id'] ?>"
                                            <?= (($_POST['rol_id'] ?? '') == $rol['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rol['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cargo">Cargo</label>
                                <input type="text" id="cargo" name="cargo"
                                       value="<?= htmlspecialchars($_POST['cargo'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Correo</label>
                                <input type="email" id="email" name="email"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="telefono_extension">Teléfono / extensión</label>
                                <input type="text" id="telefono_extension" name="telefono_extension"
                                       value="<?= htmlspecialchars($_POST['telefono_extension'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contrasena">Contraseña * (mín. 6 caracteres)</label>
                            <input type="password" id="contrasena" name="contrasena" required minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary">Registrar usuario</button>
                    </form>
                </div>
<?php require dirname(__DIR__) . '/public/includes/layout_footer.php'; ?>
