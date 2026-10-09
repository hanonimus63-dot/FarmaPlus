<?php
// header.php - Cabecera reutilizable con CSS incrustado autónomo
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
    <style>
        /* Estilos Globales y de Cabecera Incorporados */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f7f6;
            color: #333333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header & Nav */
        .header-principal {
            background-color: #0056b3;
            color: #ffffff;
            padding: 12px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }

        .contenedor-header {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-empresa a {
            color: #ffffff;
            text-decoration: none;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .logo-icono {
            background-color: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 20px;
        }

        .nav-principal ul {
            list-style: none;
            display: flex;
            gap: 15px;
        }

        .nav-principal a {
            color: #e0e0e0;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .nav-principal a:hover,
        .nav-principal a.activo {
            background-color: #003d80;
            color: #ffffff;
        }

        .usuario-info {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 4px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 13px;
        }

        .btn-logout:hover {
            background-color: #bd2130;
        }

        /* Contenedor Principal */
        .contenedor-principal {
            max-width: 1100px;
            margin: 25px auto;
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }

        .titulo-pagina {
            font-size: 24px;
            color: #0056b3;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        /* Tablas */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            margin-top: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tabla-datos th, .tabla-datos td {
            border: 1px solid #dddddd;
            padding: 10px 12px;
            text-align: left;
        }

        .tabla-datos th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
        }

        .tabla-datos tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .tabla-datos tr:hover {
            background-color: #f1f3f5;
        }

        /* Botones y Formularios */
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-primario { background-color: #0056b3; color: white; }
        .btn-primario:hover { background-color: #004085; }
        .btn-exito { background-color: #28a745; color: white; }
        .btn-exito:hover { background-color: #218838; }
        .btn-peligro { background-color: #dc3545; color: white; }
        .btn-secundario { background-color: #6c757d; color: white; }

        .card-formulario {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .grupo-form {
            margin-bottom: 15px;
        }

        .grupo-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .grupo-form input,
        .grupo-form select,
        .grupo-form textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Alertas */
        .alerta {
            padding: 12px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alerta-exito { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alerta-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alerta-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

        /* Grid Dashboard Cards */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card-dash {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-left: 4px solid #0056b3;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .card-dash h3 {
            font-size: 16px;
            color: #555555;
            margin-bottom: 10px;
        }

        .card-dash .numero {
            font-size: 28px;
            font-weight: bold;
            color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Cabecera (Header) - Requerimiento A -->
    <header class="header-principal">
        <div class="contenedor-header">
            <!-- Logotipo de empresa tipo texto -->
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
