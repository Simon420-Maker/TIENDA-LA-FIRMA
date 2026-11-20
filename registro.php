<?php
require_once __DIR__ . '/includes/auth.php';

if (usuario_actual()) {
    header('Location: index.php');
    exit;
}

$error = '';
$usuario = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario   = trim($_POST['usuario'] ?? '');
    $correo    = trim($_POST['correo'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!csrf_valido($_POST['csrf'] ?? null)) {
        $error = 'Sesión expirada. Inténtalo de nuevo.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $usuario)) {
        $error = 'El usuario debe tener entre 3 y 50 caracteres (letras, números, punto, guion o guion bajo).';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 100) {
        $error = 'Ingresa un correo electrónico válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener mínimo 6 caracteres.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo = db();
        $existe = $pdo->prepare('SELECT Usuario, Correo FROM usuarios WHERE Usuario = ? OR Correo = ? LIMIT 1');
        $existe->execute([$usuario, $correo]);
        $fila = $existe->fetch();

        if ($fila) {
            $error = strcasecmp($fila['Usuario'], $usuario) === 0
                ? 'Ese usuario ya está registrado.'
                : 'Ese correo ya está registrado.';
        } else {
            try {
                $pdo->beginTransaction();
                // Todo usuario queda ligado a un registro de la tabla clientes (la usa "ventas")
                $pdo->prepare('INSERT INTO clientes (nombre) VALUES (?)')->execute([$usuario]);
                $idCliente = (int) $pdo->lastInsertId();

                $pdo->prepare(
                    'INSERT INTO usuarios (Usuario, Correo, Password_Hash, Id_Cliente) VALUES (?, ?, ?, ?)'
                )->execute([$usuario, $correo, password_hash($password, PASSWORD_DEFAULT), $idCliente]);
                $idUsuario = (int) $pdo->lastInsertId();
                $pdo->commit();

                iniciar_sesion([
                    'Id_Usuario' => $idUsuario, 'Id_Cliente' => $idCliente,
                    'Usuario' => $usuario, 'Rol' => 'cliente',
                ]);
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $error = $e->getCode() === '23000'
                    ? 'Ese usuario o correo ya está registrado.'
                    : 'No se pudo crear la cuenta. Inténtalo de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear cuenta - Tienda Escolar</title>
<link rel="stylesheet" href="css/login.css">
</head>

<body class="register-page">
<main class="container register-container">
    <section class="card register-card">
        <div class="logo">
            <a href="index.php"><img src="img/logo.png" alt="Tienda la firma"></a>
        </div>

        <h1>Crear cuenta</h1>

        <form method="post" action="registro.php" id="registerForm">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <div class="input-group">
                <label for="registerUsername">Usuario</label>
                <input id="registerUsername" name="usuario" type="text" placeholder="Elige un usuario"
                       autocomplete="username" minlength="3" maxlength="50" value="<?= e($usuario) ?>" required>
            </div>

            <div class="input-group">
                <label for="registerEmail">Correo electrónico</label>
                <input id="registerEmail" name="correo" type="email" placeholder="nombre@correo.com"
                       autocomplete="email" maxlength="100" value="<?= e($correo) ?>" required>
            </div>

            <div class="input-group">
                <label for="registerPassword">Contraseña</label>
                <input id="registerPassword" name="password" type="password" placeholder="Mínimo 6 caracteres"
                       autocomplete="new-password" minlength="6" required>
            </div>

            <div class="input-group">
                <label for="confirmPassword">Confirmar contraseña</label>
                <input id="confirmPassword" name="confirmar" type="password" placeholder="Repite tu contraseña"
                       autocomplete="new-password" minlength="6" required>
            </div>

            <?php if ($error): ?>
                <p class="auth-message error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="login-btn">Crear cuenta</button>
        </form>

        <a href="login.php" class="home-link">Ya tengo cuenta: iniciar sesión</a>
    </section>
</main>
</body>
</html>
