<?php
// inventario.php - Módulo de Inventario (Gestión de Medicamentos)
require_once 'header.php';

$mensaje = '';
$tipo_mensaje = 'exito';

// Procesar Formulario de Registro / Edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'guardar') {
        $codigo = trim($_POST['codigo_barras'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $laboratorio = trim($_POST['laboratorio'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $requiere_receta = isset($_POST['requiere_receta']) ? 1 : 0;

        if (!empty($codigo) && !empty($nombre) && !empty($laboratorio) && $precio > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO medicamentos (codigo_barras, nombre, laboratorio, precio, stock, requiere_receta) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$codigo, $nombre, $laboratorio, $precio, $stock, $requiere_receta]);
                $mensaje = 'Medicamento registrado correctamente.';
            } catch (PDOException $e) {
                $mensaje = 'Error al registrar: El código de barras ya existe o hubo un problema en la BD.';
                $tipo_mensaje = 'error';
            }
        } else {
            $mensaje = 'Por favor complete los campos obligatorios válidos.';
            $tipo_mensaje = 'error';
        }
    } elseif ($_POST['accion'] === 'eliminar' && esAdmin()) {
        $id_eliminar = intval($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id = ?");
            $stmt->execute([$id_eliminar]);
            $mensaje = 'Medicamento eliminado del inventario.';
        } catch (PDOException $e) {
            $mensaje = 'No se puede eliminar el medicamento porque posee registros de dispensación asociados.';
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener lista de medicamentos
$medicamentos = $pdo->query("SELECT * FROM medicamentos ORDER BY nombre ASC")->fetchAll();
?>

<h1 class="titulo-pagina">Módulo de Inventario (Medicamentos)</h1>

<?php if (!empty($mensaje)): ?>
    <div class="alerta alerta-<?= $tipo_mensaje ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<!-- Formulario para Registrar Medicamento -->
<div class="card-formulario">
    <h2 style="font-size: 18px; margin-bottom: 15px; color: #0056b3;">Registrar Nuevo Medicamento</h2>
    <form method="POST" action="inventario.php">
        <input type="hidden" name="accion" value="guardar">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="grupo-form">
                <label for="codigo_barras">Código de Barras *</label>
                <input type="text" id="codigo_barras" name="codigo_barras" required placeholder="Ej. 7790001099">
            </div>

            <div class="grupo-form">
                <label for="nombre">Nombre Comercial / Droga *</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej. Paracetamol 500mg">
            </div>

            <div class="grupo-form">
                <label for="laboratorio">Laboratorio *</label>
                <input type="text" id="laboratorio" name="laboratorio" required placeholder="Ej. Bayer">
            </div>

            <div class="grupo-form">
                <label for="precio">Precio ($) *</label>
                <input type="number" step="0.01" id="precio" name="precio" required placeholder="0.00">
            </div>

            <div class="grupo-form">
                <label for="stock">Cantidad en Stock *</label>
                <input type="number" id="stock" name="stock" required placeholder="0" value="10">
            </div>

            <div class="grupo-form" style="display: flex; align-items: center; gap: 8px; margin-top: 25px;">
                <input type="checkbox" id="requiere_receta" name="requiere_receta" value="1" style="width: auto;">
                <label for="requiere_receta" style="margin-bottom: 0; font-weight: normal;">¿Requiere Receta Médica?</label>
            </div>
        </div>

        <button type="submit" class="btn btn-exito" style="margin-top: 10px;">+ Registrar Medicamento</button>
    </form>
</div>

<!-- Listado de Medicamentos -->
<h2 style="font-size: 18px; color: #0056b3; margin-top: 20px;">Listado de Productos en Stock</h2>
<table class="tabla-datos">
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre / Descripción</th>
            <th>Laboratorio</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Receta</th>
            <?php if (esAdmin()): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicamentos as $med): ?>
            <tr>
                <td><?= $med['id'] ?></td>
                <td><code><?= htmlspecialchars($med['codigo_barras']) ?></code></td>
                <td><strong><?= htmlspecialchars($med['nombre']) ?></strong></td>
                <td><?= htmlspecialchars($med['laboratorio']) ?></td>
                <td>$<?= number_format($med['precio'], 2) ?></td>
                <td>
                    <span style="<?= $med['stock'] <= 15 ? 'color: red; font-weight: bold;' : '' ?>">
                        <?= $med['stock'] ?> u.
                    </span>
                </td>
                <td>
                    <?php if ($med['requiere_receta']): ?>
                        <span style="color: #d9534f; font-weight: bold;">Sí (Bajo Receta)</span>
                    <?php else: ?>
                        <span style="color: #5cb85c;">Venta Libre</span>
                    <?php endif; ?>
                </td>
                <?php if (esAdmin()): ?>
                    <td>
                        <form method="POST" action="inventario.php" onsubmit="return confirm('¿Está seguro de eliminar este producto?');" style="display: inline;">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $med['id'] ?>">
                            <button type="submit" class="btn btn-peligro" style="padding: 3px 8px; font-size: 12px;">Eliminar</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>
