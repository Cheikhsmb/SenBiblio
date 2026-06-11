<?php
// config.php
// Database connection settings for the Senegalese Library system.

session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'library_senegal');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_NAME', 'Senegal Digital Library');

define('COLOR_PRIMARY', '#1f3a5f');
define('COLOR_ACCENT', '#f0a500');

function getPDO()
{
    static $pdo;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            die('Database connection failed: ' . htmlspecialchars($exception->getMessage()));
        }
    }

    return $pdo;
}

function redirectIfNotAuthenticated()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function flashMessage()
{
    if (!empty($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

function setFlash($message, $type = 'success')
{
    $_SESSION['flash_message'] = [
        'text' => $message,
        'type' => $type,
    ];
}
