<?php
// config.php - edit DB_USER & DB_PASS
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'raffle_app');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('❌ Error al conectar a la base de datos: ' . $e->getMessage());
}

// BASE_URL automatic
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
define('BASE_URL', $protocol . $host . $script_dir);

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function isLoggedIn(){ return !empty($_SESSION['user']); }
function currentUser(){ return $_SESSION['user'] ?? null; }
function isAdmin(){ $u = currentUser(); return $u && ($u['rol'] === 'admin'); }

function csrf_token(){
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));
    return $_SESSION['csrf'];
}
function verify_csrf($token){ return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token); }
?>