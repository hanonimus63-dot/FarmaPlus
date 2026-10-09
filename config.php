<?php
// config.php - Configuración de conexión a Base de Datos compatible con XAMPP, DBeaver y Railway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables de entorno para Railway o Servidor local
$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';
$dbname = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'farmaplus';
$username = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Si la base de datos aún no existe en local, intentamos conectar sin DB
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
    } catch (PDOException $ex) {
        die("Error de conexión a la base de datos: " . $ex->getMessage());
    }
}

// Funciones de utilidad para autenticación y sesiones
function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

function requerirAutenticacion() {
    if (!estaAutenticado()) {
        header("Location: login.php");
        exit();
    }
}

function requerirAdmin() {
    requerirAutenticacion();
    if (!esAdmin()) {
        header("Location: index.php?error=acceso_denegado");
        exit();
    }
}
?>
