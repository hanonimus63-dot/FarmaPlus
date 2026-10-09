<?php
// usuarios.php - Módulo de Control de Acceso y Gestión de Usuarios (Exclusivo Admin)
require_once 'header.php';
requerirAdmin(); // Control de acceso por rol

$mensaje = '';
$tipo_mensaje = 'exito';

// Crear nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $rol = $_POST['rol'] ?? 'farmaceutico';

    if (!empty($username) && !empty($password) && !empty($nombre)) {
        try {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nombre, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $pass_hash, $nombre, $rol]);
            $mensaje = "Usuario '$username' creado exitosamente con rol de $rol.";
        } catch (PDOException $e) {
            $mensaje = "El nombre de usuario ya se encuentra registrado.";
            $tipo_mensaje = 'error';
        }
    } else {
        $mensaje = "Por favor complete todos los campos obligatorios.";
        $tipo_mensaje = 'error';
    }
}

$usuarios = $pdo->query("SELECT id, username, nombre, rol, creado_en FROM usuarios ORDER BY id ASC")->fetchAll();
?>

<h1 class="titulo-pagina">Módulo de Control de Usuarios (Administración)</h1>

<?php if (!empty($mensaje)): ?>
    <div class="alerta alerta-<?= $tipo_mensaje ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<!-- Formulario para Nuevo Usuario -->
<div class="card-formulario">
    <h2 style="font-size: 18px; margin-bottom: 15px; color: #0056b3;">Registrar Nuevo Usuario del Sistema</h2>
    <form method="POST" action="usuarios.php">
        <input type="hidden" name="accion" value="crear">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="grupo-form">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej. Ana Martínez">
            </div>

            <div class="grupo-form">
                <label for="username">Usuario (Login) *</label>
                <input type="text" id="username" name="username" required placeholder="Ej. amartinez">
            </div>

            <div class="grupo-form">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="grupo-form">
                <label for="rol">Rol de Usuario *</label>
                <select id="rol" name="rol" required>
                    <option value="farmaceutico">Farmacéutico (Ventas e Inventario)</option>
                    <option value="admin">Administrador (Control Total)</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-exito" style="margin-top: 10px;">+ Registrar Usuario</button>
    </form>
</div>

<!-- Listado de Usuarios -->
<h2 style="font-size: 18px; color: #0056b3; margin-top: 20px;">Usuarios Registrados en la Aplicación</h2>
<table class="tabla-datos">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre Completo</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Fecha Alta</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><strong><?= htmlspecialchars($u['nombre']) ?></strong></td>
                <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                <td>
                    <?php if ($u['rol'] === 'admin'): ?>
                        <span style="background-color: #0056b3; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px;">Administrador</span>
                    <?php else: ?>
                        <span style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px;">Farmacéutico</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d/m/Y', strtotime($u['creado_en'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>
