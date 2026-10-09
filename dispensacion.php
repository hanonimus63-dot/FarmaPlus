<?php
// dispensacion.php - Módulo de Dispensación y Venta de Medicamentos
require_once 'header.php';

$mensaje = '';
$tipo_mensaje = 'exito';

// Procesar Registro de Dispensación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'dispensar') {
    $paciente = trim($_POST['paciente_nombre'] ?? '');
    $seguro_id = intval($_POST['seguro_id'] ?? 0);
    $nro_afiliado = trim($_POST['nro_afiliado'] ?? '');
    $medicamentos_ids = $_POST['medicamento_id'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];

    if (!empty($paciente) && !empty($medicamentos_ids)) {
        try {
            $pdo->beginTransaction();

            // Consultar seguro
            $stmt_seg = $pdo->prepare("SELECT * FROM seguros WHERE id = ?");
            $stmt_seg->execute([$seguro_id]);
            $seguro = $stmt_seg->fetch();

            $porcentaje_cobertura = $seguro ? floatval($seguro['cobertura_porcentaje']) : 0;

            $subtotal_general = 0;
            $detalles_procesar = [];

            // Calcular totales y verificar stock
            for ($i = 0; $i < count($medicamentos_ids); $i++) {
                $med_id = intval($medicamentos_ids[$i]);
                $cant = intval($cantidades[$i]);

                if ($med_id > 0 && $cant > 0) {
                    $stmt_m = $pdo->prepare("SELECT * FROM medicamentos WHERE id = ?");
                    $stmt_m->execute([$med_id]);
                    $med = $stmt_m->fetch();

                    if ($med) {
                        if ($med['stock'] < $cant) {
                            throw new Exception("Stock insuficiente para: " . $med['nombre'] . " (Disponible: " . $med['stock'] . ")");
                        }

                        $precio_u = floatval($med['precio']);
                        $sub = $precio_u * $cant;
                        $subtotal_general += $sub;

                        $detalles_procesar[] = [
                            'medicamento_id' => $med['id'],
                            'cantidad' => $cant,
                            'precio_unitario' => $precio_u,
                            'subtotal' => $sub
                        ];

                        // Actualizar Stock
                        $stmt_upd = $pdo->prepare("UPDATE medicamentos SET stock = stock - ? WHERE id = ?");
                        $stmt_upd->execute([$cant, $med['id']]);
                    }
                }
            }

            if (empty($detalles_procesar)) {
                throw new Exception("Debe seleccionar al menos un medicamento válido.");
            }

            $monto_cobertura = ($subtotal_general * $porcentaje_cobertura) / 100;
            $monto_total = $subtotal_general - $monto_cobertura;

            // Insertar Cabecera de Dispensación
            $stmt_disp = $pdo->prepare("INSERT INTO dispensaciones (usuario_id, paciente_nombre, seguro_id, nro_afiliado, monto_subtotal, monto_cobertura, monto_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_disp->execute([
                $_SESSION['usuario_id'],
                $paciente,
                $seguro_id ?: null,
                $nro_afiliado,
                $subtotal_general,
                $monto_cobertura,
                $monto_total
            ]);

            $dispensacion_id = $pdo->lastInsertId();

            // Insertar Detalles
            $stmt_det = $pdo->prepare("INSERT INTO dispensacion_detalles (dispensacion_id, medicamento_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($detalles_procesar as $det) {
                $stmt_det->execute([
                    $dispensacion_id,
                    $det['medicamento_id'],
                    $det['cantidad'],
                    $det['precio_unitario'],
                    $det['subtotal']
                ]);
            }

            $pdo->commit();
            $mensaje = "Dispensación #$dispensacion_id registrada exitosamente. Total abonado: $" . number_format($monto_total, 2);

        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje = "Error en dispensación: " . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = "Por favor complete el nombre del paciente y seleccione productos.";
        $tipo_mensaje = 'error';
    }
}

// Datos para combos
$seguros = $pdo->query("SELECT * FROM seguros WHERE estado = 'activo'")->fetchAll();
$medicamentos = $pdo->query("SELECT * FROM medicamentos WHERE stock > 0 ORDER BY nombre ASC")->fetchAll();

// Historial de dispensaciones
$historial = $pdo->query("
    SELECT d.*, u.nombre as farmaceutico, s.nombre as seguro_nombre 
    FROM dispensaciones d 
    JOIN usuarios u ON d.usuario_id = u.id 
    LEFT JOIN seguros s ON d.seguro_id = s.id 
    ORDER BY d.id DESC LIMIT 10
")->fetchAll();
?>

<h1 class="titulo-pagina">Módulo de Dispensación de Medicamentos</h1>

<?php if (!empty($mensaje)): ?>
    <div class="alerta alerta-<?= $tipo_mensaje ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<!-- Formulario Nueva Dispensación -->
<div class="card-formulario">
    <h2 style="font-size: 18px; margin-bottom: 15px; color: #0056b3;">Nueva Receta / Dispensación</h2>
    <form method="POST" action="dispensacion.php">
        <input type="hidden" name="accion" value="dispensar">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
            <div class="grupo-form">
                <label for="paciente_nombre">Nombre del Paciente *</label>
                <input type="text" id="paciente_nombre" name="paciente_nombre" required placeholder="Ej. Juan Pérez">
            </div>

            <div class="grupo-form">
                <label for="seguro_id">Seguro Médico / Obra Social *</label>
                <select id="seguro_id" name="seguro_id" required>
                    <?php foreach ($seguros as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['nombre']) ?> (Cobertura: <?= number_format($s['cobertura_porcentaje'], 0) ?>%)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grupo-form">
                <label for="nro_afiliado">N° de Afiliado / Carnet</label>
                <input type="text" id="nro_afiliado" name="nro_afiliado" placeholder="Ej. AF-98765432">
            </div>
        </div>

        <h3 style="font-size: 15px; margin: 15px 0 10px 0; color: #333;">Selección de Medicamentos</h3>
        
        <table class="tabla-datos" id="tabla-productos">
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th style="width: 120px;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="medicamento_id[]" required style="width: 100%; padding: 6px;">
                            <option value="">-- Seleccionar Producto --</option>
                            <?php foreach ($medicamentos as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    <?= htmlspecialchars($m['nombre']) ?> - $<?= number_format($m['precio'], 2) ?> (Stock: <?= $m['stock'] ?>) <?= $m['requiere_receta'] ? '[Bajo Receta]' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="number" name="cantidad[]" min="1" value="1" required style="width: 100%; padding: 6px;">
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-exito" style="margin-top: 15px;">Finalizar y Dispensar Medicamentos</button>
    </form>
</div>

<!-- Historial Reciente -->
<h2 style="font-size: 18px; color: #0056b3; margin-top: 25px;">Últimas Dispensaciones Registradas</h2>
<table class="tabla-datos">
    <thead>
        <tr>
            <th>N° Folio</th>
            <th>Fecha</th>
            <th>Paciente</th>
            <th>Seguro</th>
            <th>Subtotal</th>
            <th>Cobertura</th>
            <th>Total Paciente</th>
            <th>Atentido por</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($historial as $h): ?>
            <tr>
                <td>#<?= sprintf('%05d', $h['id']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></td>
                <td><strong><?= htmlspecialchars($h['paciente_nombre']) ?></strong></td>
                <td><?= htmlspecialchars($h['seguro_nombre'] ?? 'Particular') ?></td>
                <td>$<?= number_format($h['monto_subtotal'], 2) ?></td>
                <td style="color: #28a745;">-$<?= number_format($h['monto_cobertura'], 2) ?></td>
                <td><strong>$<?= number_format($h['monto_total'], 2) ?></strong></td>
                <td><?= htmlspecialchars($h['farmaceutico']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>
