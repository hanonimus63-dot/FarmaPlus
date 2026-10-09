<?php
// config.php - Configuración de conexión PDO a MySQL para Local (XAMPP/DBeaver) y Servidor en Producción (Railway)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables de entorno inyectadas automáticamente por Railway (MySQL) o fallback local para XAMPP/DBeaver
$host     = getenv('MYSQLHOST')     ?: getenv('MYSQL_HOST')     ?: getenv('DB_HOST')     ?: '127.0.0.1';
$port     = getenv('MYSQLPORT')     ?: getenv('MYSQL_PORT')     ?: getenv('DB_PORT')     ?: '3306';
$dbname   = getenv('MYSQLDATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME')     ?: 'farmaplus';
$username = getenv('MYSQLUSER')     ?: getenv('MYSQL_USER')     ?: getenv('DB_USER')     ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';

try {
    // Intentar conectar con la base de datos seleccionada
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    // En caso de estar en local y que la base de datos 'farmaplus' no exista aún en MySQL/DBeaver, se crea automáticamente
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
    } catch (PDOException $ex) {
        die("<div style='padding:20px; font-family:sans-serif; color:red;'>
                <h2>Error de conexión a la Base de Datos MySQL</h2>
                <p>No se pudo conectar a MySQL. Verifique sus credenciales en XAMPP, DBeaver o las variables de entorno de Railway.</p>
                <small>Detalle técnico: " . htmlspecialchars($ex->getMessage()) . "</small>
             </div>");
    }
}

// Funciones de validación de autenticación y roles de usuario
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
