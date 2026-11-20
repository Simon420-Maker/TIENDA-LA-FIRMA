<?php
/**
 * Diagnóstico de la base de datos y de los usuarios (solo funciona desde localhost).
 * Abre: http://localhost/tienda_la_firma/diagnostico.php
 * BÓRRALO cuando todo funcione.
 */
if (!in_array($_SERVER["REMOTE_ADDR"] ?? "", ["127.0.0.1", "::1"], true)) {
    http_response_code(403);
    exit("Solo disponible desde localhost.");
}
require_once __DIR__ . "/config/db.php";
function h($t) { return htmlspecialchars((string) $t, ENT_QUOTES, "UTF-8"); }

$msg = "";
$pdo = null;
$errorConexion = "";
try {
    $pdo = db();
} catch (Throwable $e) {
    $errorConexion = $e->getMessage();
}

// Restablecer la contraseña de un usuario (útil si lo crearon a mano en phpMyAdmin)
if ($pdo && $_SERVER["REQUEST_METHOD"] === "POST") {
    $u = trim($_POST["usuario"] ?? "");
    $p = $_POST["password"] ?? "";
    if ($u !== "" && strlen($p) >= 6) {
        $st = $pdo->prepare("UPDATE usuarios SET Password_Hash = ? WHERE Usuario = ?");
        $st->execute([password_hash($p, PASSWORD_DEFAULT), $u]);
        $msg = $st->rowCount() ? "Contraseña de «" . h($u) . "» actualizada. Ya puedes iniciar sesión." : "No existe el usuario «" . h($u) . "».";
    } else {
        $msg = "Escribe un usuario y una contraseña de mínimo 6 caracteres.";
    }
}
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Diagnóstico</title>
<style>
body{font-family:Arial,sans-serif;max-width:820px;margin:30px auto;padding:0 16px;color:#222}
.ok{color:#0a7a2f;font-weight:bold}.mal{color:#b00020;font-weight:bold}
table{border-collapse:collapse;width:100%;margin:10px 0}td,th{border:1px solid #ccc;padding:6px 8px;text-align:left;font-size:14px}
input{padding:6px;margin:4px 4px 4px 0}
</style></head><body>
<h1>Diagnóstico Tienda La Firma</h1>

<h2>1. Conexión a MySQL</h2>
<?php if (!$pdo): ?>
  <p class="mal">✗ No conecta.</p><pre><?= h($errorConexion) ?></pre>
  <p>Revisa que MySQL esté en verde en XAMPP, que la base se llame <b>tienda_la_firma</b> (impórtala desde phpMyAdmin) y el puerto en <code>config/db.php</code>.</p>
<?php else: ?>
  <p class="ok">✓ Conectado a la base «<?= h(DB_NAME) ?>».</p>

  <h2>2. Tablas</h2>
  <table><tr><th>Tabla</th><th>Filas</th></tr>
  <?php foreach (["clientes","usuarios","productos","plato_ingredientes","pedidos","ventas"] as $t):
      try { $n = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); $ok = true; }
      catch (Throwable $e) { $n = "NO EXISTE"; $ok = false; } ?>
    <tr><td><?= h($t) ?></td><td class="<?= $ok ? "ok" : "mal" ?>"><?= h($n) ?></td></tr>
  <?php endforeach; ?></table>

  <h2>3. Usuarios registrados</h2>
  <?php try { $us = $pdo->query("SELECT Id_Usuario, Usuario, Correo, Password_Hash, Rol FROM usuarios")->fetchAll(); } catch (Throwable $e) { $us = []; echo "<p class=\"mal\">" . h($e->getMessage()) . "</p>"; } ?>
  <?php if (!$us): ?><p>No hay usuarios todavía. Crea uno en <a href="registro.php">registro.php</a>.</p><?php else: ?>
  <table><tr><th>ID</th><th>Usuario</th><th>Correo</th><th>Contraseña guardada</th></tr>
  <?php foreach ($us as $r):
      $valido = preg_match("/^\\\$2[aby]\\\$\\d\\d\\\$.{53}$/", $r["Password_Hash"]) === 1; ?>
    <tr><td><?= h($r["Id_Usuario"]) ?></td><td><?= h($r["Usuario"]) ?></td><td><?= h($r["Correo"]) ?></td>
    <td class="<?= $valido ? "ok" : "mal" ?>"><?= $valido ? "✓ cifrada correctamente" : "✗ NO está cifrada (por eso no deja entrar)" ?></td></tr>
  <?php endforeach; ?></table><?php endif; ?>

  <h2>4. Restablecer contraseña</h2>
  <?php if ($msg): ?><p><b><?= $msg ?></b></p><?php endif; ?>
  <form method="post">
    <input name="usuario" placeholder="Usuario" required>
    <input name="password" type="password" placeholder="Nueva contraseña (mín. 6)" required>
    <button>Guardar</button>
  </form>
<?php endif; ?>
</body></html>
