<?php
// Update these values for your local MySQL setup.
const DB_HOST = '127.0.0.1';
const DB_NAME = 'estatedesk';
const DB_USER = 'root';
const DB_PASS = '';

// Auto-detect the URL path of the project (works in /htdocs/estatedesk or a vhost root).
$root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$proj = str_replace('\\', '/', dirname(__DIR__));
define('BASE_URL', rtrim(str_replace($root, '', $proj), '/') . '/');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
