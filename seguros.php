<?php
// seguros.php - Módulo de Seguros Médicos / Obras Sociales
require_once 'header.php';

$mensaje = '';
$tipo_mensaje = 'exito';

// Guardar nuevo Seguro Médico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'guardar') {
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $cobertura = floatval($_POST['cobertura_porcentaje'] ?? 0);
        $telefono = trim($_POST['telefono'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';

        if (!empty($codigo) && !empty($nombre) && $cobertura >= 0 && $cobertura <= 100) {
            try {
                $stmt = $pdo->prepare("INSERT INTO seguros (codigo, nombre, cobertura_porcentaje, telefono, estado) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$codigo, $nombre, $cobertura, $telefono, $estado]);
                $mensaje = 'Seguro Médico / Obra Social registrada con éxito.';
            } catch (PDOException $e) {
                $mensaje = 'Error al registrar: El código del seguro ya existe.';
                $tipo_mensaje = 'error';
            }
        } else {
            $mensaje = 'Datos no válidos. Por favor revise el formulario.';
            $tipo_mensaje = 'error';
        }
    }
}

$seguros = $pdo->query("SELECT * FROM seguros ORDER BY id ASC")->fetchAll();
?>

<h1 class="titulo-pagina">Módulo de Seguros Médicos / Obras Sociales</h1>

<?php if (!empty($mensaje)): ?>
    <div class="alerta alerta-<?= $tipo_mensaje ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<!-- Formulario para Nuevo Seguro Médico -->
<div class="card-formulario">
    <h2 style="font-size: 18px; margin-bottom: 15px; color: #0056b3;">Agregar Nueva Entidad de Seguro / Cobertura</h2>
    <form method="POST" action="seguros.php">
        <input type="hidden" name="accion" value="guardar">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="grupo-form">
                <label for="codigo">Código Identificador *</label>
                <input type="text" id="codigo" name="codigo" required placeholder="Ej. SEG-GALENO">
            </div>

            <div class="grupo-form">
                <label for="nombre">Nombre de la Entidad / Cobertura *</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej. Galeno Argentina">
            </div>

            <div class="grupo-form">
                <label for="cobertura_porcentaje">% Cobertura / Descuento *</label>
                <input type="number" step="0.01" min="0" max="100" id="cobertura_porcentaje" name="cobertura_porcentaje" required placeholder="Ej. 40.00">
            </div>

            <div class="grupo-form">
                <label for="telefono">Teléfono de Autorización</label>
                <input type="text" id="telefono" name="telefono" placeholder="Ej. 0800-222-1111">
            </div>

            <div class="grupo-form">
                <label for="estado">Estado *</label>
                <select id="estado" name="estado">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-exito" style="margin-top: 10px;">+ Registrar Seguro Médico</button>
    </form>
</div>

<!-- Listado de Seguros -->
<h2 style="font-size: 18px; color: #0056b3; margin-top: 20px;">Convenios y Obras Sociales Registradas</h2>
<table class="tabla-datos">
    <thead>
        <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre Entidad</th>
            <th>% Cobertura</th>
            <th>Teléfono Contacto</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seguros as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><code><?= htmlspecialchars($s['codigo']) ?></code></td>
                <td><strong><?= htmlspecialchars($s['nombre']) ?></strong></td>
                <td><span style="color: #0056b3; font-weight: bold;"><?= number_format($s['cobertura_porcentaje'], 2) ?>%</span></td>
                <td><?= htmlspecialchars($s['telefono']) ?></td>
                <td>
                    <?php if ($s['estado'] == 'activo'): ?>
                        <span style="color: green; font-weight: bold;">Activo</span>
                    <?php else: ?>
                        <span style="color: gray;">Inactivo</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>
