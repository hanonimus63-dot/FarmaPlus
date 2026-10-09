<?php
// index.php - Dashboard Principal
require_once 'header.php';

// Estadísticas de la farmacia
$total_meds = $pdo->query("SELECT COUNT(*) FROM medicamentos")->fetchColumn();
$total_seguros = $pdo->query("SELECT COUNT(*) FROM seguros WHERE estado = 'activo'")->fetchColumn();
$total_dispensaciones = $pdo->query("SELECT COUNT(*) FROM dispensaciones")->fetchColumn();
$stock_bajo = $pdo->query("SELECT COUNT(*) FROM medicamentos WHERE stock <= 15")->fetchColumn();
?>

<h1 class="titulo-pagina">Panel Principal - FarmaPlus</h1>

<?php if (isset($_GET['error']) && $_GET['error'] == 'acceso_denegado'): ?>
    <div class="alerta alerta-error">
        Acceso Denegado: No tiene permisos suficientes para acceder a esa sección.
    </div>
<?php endif; ?>

<div class="alerta alerta-info">
    Bienvenido al sistema <strong>FarmaPlus</strong>. Ha iniciado sesión como <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong> [Rol: <?= strtoupper(htmlspecialchars($_SESSION['usuario_rol'])) ?>].
</div>

<div class="grid-cards">
    <div class="card-dash">
        <h3>Medicamentos en Inventario</h3>
        <div class="numero"><?= $total_meds ?></div>
    </div>
    
    <div class="card-dash">
        <h3>Alertas de Stock Bajo</h3>
        <div class="numero" style="color: #dc3545;"><?= $stock_bajo ?></div>
    </div>

    <div class="card-dash">
        <h3>Seguros Médicos Activos</h3>
        <div class="numero"><?= $total_seguros ?></div>
    </div>

    <div class="card-dash">
        <h3>Dispensaciones Realizadas</h3>
        <div class="numero"><?= $total_dispensaciones ?></div>
    </div>
</div>

<h2 style="margin-top: 30px; margin-bottom: 15px; color: #0056b3;">Accresos Rápidos a Módulos</h2>
<table class="tabla-datos">
    <thead>
        <tr>
            <th>Módulo</th>
            <th>Descripción</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Módulo Inventario</strong></td>
            <td>Gestión y registro de medicamentos, stock y precios.</td>
            <td><a href="inventario.php" class="btn btn-primario">Ir a Inventario</a></td>
        </tr>
        <tr>
            <td><strong>Módulo Dispensación</strong></td>
            <td>Entrega de productos con receta, cálculo de descuento por Seguro Médico.</td>
            <td><a href="dispensacion.php" class="btn btn-exito">Realizar Dispensación</a></td>
        </tr>
        <tr>
            <td><strong>Módulo Seguros Médicos</strong></td>
            <td>Administración de obras sociales y porcentajes de cobertura.</td>
            <td><a href="seguros.php" class="btn btn-secundario">Ver Seguros</a></td>
        </tr>
        <?php if (esAdmin()): ?>
        <tr>
            <td><strong>Módulo de Usuarios</strong></td>
            <td>Gestión de cuentas y asignación de roles (Exclusivo Administrador).</td>
            <td><a href="usuarios.php" class="btn btn-primario">Gestionar Usuarios</a></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>
