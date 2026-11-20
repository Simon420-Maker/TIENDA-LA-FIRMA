<?php
/**
 * Conexión a la base de datos.
 * En XAMPP el usuario es "root" y la contraseña suele estar vacía.
 */
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;   // si en XAMPP cambiaste el puerto de MySQL (ej. 3307), cámbialo aquí
const DB_NAME = 'tienda_la_firma';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            $mensaje = 'No se pudo conectar a la base de datos. '
                     . 'Asegúrate de que MySQL esté encendido en XAMPP e importa el esquema desde database/tienda_la_firma.sql. '
                     . 'Detalle técnico: ' . $e->getMessage();
            if (defined('API_REQUEST')) {
                throw new RuntimeException($mensaje);
            }
            http_response_code(500);
            die('<p style="font-family:Arial;padding:30px;color:#8B0000">' . htmlspecialchars($mensaje) . '</p>');
        }
    }

    return $pdo;
}
