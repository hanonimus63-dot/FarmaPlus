<?php
// header.php - Cabecera reutilizable visible en todas las páginas según requerimiento A y C
require_once 'config.php';
requerirAutenticacion();

$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmaPlus - Sistema de Gestión de Farmacia</title>
    <link rel="stylesheet" href="css/estilos.css?v=1.1">
</head>
<body>

    <!-- Cabecera (Header) - Requerimiento A -->
    <header class="header-principal">
        <div class="contenedor-header">
            <!-- Logotipo de empresa tipo texto (Sin imágenes de logos por requerimiento) -->
            <div class="logo-empresa">
                <a href="index.php">
                    <span class="logo-icono">+</span> FarmaPlus
                </a>
            </div>

            <!-- Menú de navegación / Selección - Requerimiento C -->
            <nav class="nav-principal">
                <ul>
                    <li><a href="index.php" class="<?= $pagina_actual == 'index.php' ? 'activo' : '' ?>">Inicio</a></li>
                    <li><a href="inventario.php" class="<?= $pagina_actual == 'inventario.php' ? 'activo' : '' ?>">Inventario</a></li>
                    <li><a href="dispensacion.php" class="<?= $pagina_actual == 'dispensacion.php' ? 'activo' : '' ?>">Dispensación</a></li>
                    <li><a href="seguros.php" class="<?= $pagina_actual == 'seguros.php' ? 'activo' : '' ?>">Seguros Médicos</a></li>
                    <?php if (esAdmin()): ?>
                        <li><a href="usuarios.php" class="<?= $pagina_actual == 'usuarios.php' ? 'activo' : '' ?>">Usuarios</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Info Usuario y Cerrar Sesión -->
            <div class="usuario-info">
                <span>Hola, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong> (<?= htmlspecialchars($_SESSION['usuario_rol']) ?>)</span>
                <a href="logout.php" class="btn-logout">Salir</a>
            </div>
        </div>
    </header>

    <main class="contenedor-principal">
