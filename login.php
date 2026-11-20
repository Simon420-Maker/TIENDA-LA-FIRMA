<?php
require_once __DIR__ . '/includes/auth.php';

$next = destino_seguro($_POST['next'] ?? $_GET['next'] ?? null);

if (usuario_actual()) {
    header('Location: ' . $next);
    exit;
}

$error = '';
$exito = isset($_GET['registrado']) ? 'Cuenta creada. Ya puedes iniciar sesión.' : '';
$usuarioIngresado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioIngresado = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_valido($_POST['csrf'] ?? null)) {
        $error = 'Sesión expirada. Inténtalo de nuevo.';
    } elseif ($usuarioIngresado === '' || $password === '') {
        $error = 'Ingresa tu usuario y contraseña.';
    } else {
        $st = db()->prepare(
            'SELECT Id_Usuario, Id_Cliente, Usuario, Password_Hash, Rol
               FROM usuarios WHERE Usuario = ? OR Correo = ? LIMIT 1'
        );
        $st->execute([$usuarioIngresado, $usuarioIngresado]);
        $u = $st->fetch();

        if ($u && password_verify($password, $u['Password_Hash'])) {
            iniciar_sesion($u);
            header('Location: ' . $next);
            exit;
        }
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Tienda Escolar</title>
<link rel="stylesheet" href="css/login.css">
</head>

<body>

<div class="container">

    <div class="card contact-card" id="contactos">

        <div class="logo">
            <a href="index.php"><img src="img/logo.png" alt="Logo"></a>
        </div>

        <h1>Contactos</h1>

        <div class="contact-info">
            <p><strong>Correo:</strong><br>
            contacto@tiendaescolar.com</p>

            <p><strong>Teléfono:</strong><br>
            +57 300 000 0000</p>

            <p><strong>Dirección:</strong><br>
            Cafetería Escolar</p>

            <p><strong>Horario:</strong><br>
            9:00 AM - 4:00 PM</p>
        </div>

        <a href="#" class="btn back-btn">Volver al Login</a>

    </div>

    <div class="card login-card">

        <div class="logo">
            <a href="index.php"><img src="img/logo.png" alt="Logo"></a>
        </div>

        <h1>Iniciar Sesión</h1>

        <form method="post" action="login.php" id="loginForm">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="next" value="<?= e($next) ?>">

            <div class="input-group">
                <label for="loginUsername">Usuario</label>
                <input id="loginUsername" name="usuario" type="text" placeholder="Ingresa tu usuario"
                       autocomplete="username" value="<?= e($usuarioIngresado) ?>" required>
            </div>

            <div class="input-group">
                <label for="loginPassword">Contraseña</label>
                <input id="loginPassword" name="password" type="password" placeholder="Ingresa tu contraseña"
                       autocomplete="current-password" required>
            </div>

            <?php if ($error): ?>
                <p class="auth-message error" role="alert"><?= e($error) ?></p>
            <?php elseif ($exito): ?>
                <p class="auth-message success" role="status"><?= e($exito) ?></p>
            <?php endif; ?>

            <button type="submit" class="login-btn">Entrar</button>
        </form>

        <div class="create-account">
            ¿No tienes cuenta?
            <a href="registro.php">Crear cuenta</a>
        </div>

        <a href="#contactos" class="btn contact-btn">Contactos</a>

        <a href="index.php" class="home-link">← Volver al inicio</a>

    </div>

</div>

</body>
</html>
