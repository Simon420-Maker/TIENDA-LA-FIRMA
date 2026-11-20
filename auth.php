<?php
/**
 * Sesión, usuario actual, CSRF y funciones de ayuda comunes.
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuario_actual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function iniciar_sesion(array $u): void
{
    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'         => (int) $u['Id_Usuario'],
        'id_cliente' => $u['Id_Cliente'] !== null ? (int) $u['Id_Cliente'] : null,
        'usuario'    => $u['Usuario'],
        'rol'        => $u['Rol'],
    ];
}

/** Escapa texto para imprimirlo en HTML. */
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function formato_precio($valor): string
{
    return '$' . number_format((float) $valor, 0, ',', '.');
}

/** Devuelve la ruta de la imagen de un producto (archivo local o URL). */
function ruta_imagen(?string $archivo): string
{
    if (!$archivo) {
        return 'img/sin-imagen.svg';
    }
    if (preg_match('#^https?://#i', $archivo)) {
        return $archivo;
    }
    return 'img/productos/' . rawurlencode($archivo);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valido(?string $token): bool
{
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

/** Páginas a las que se permite volver después de iniciar sesión. */
function destino_seguro(?string $next): string
{
    $permitidas = ['index.php', 'menu.php', 'almuerzos.php', 'pedidos.php', 'buscar.php'];
    return in_array($next, $permitidas, true) ? $next : 'index.php';
}
