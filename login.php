<?php
// login.php - Requerimiento D: Control de acceso según roles con usuario y contraseña
require_once 'config.php';

if (estaAutenticado()) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Por favor complete todos los campos.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Guardar sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_username'] = $usuario['username'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            header("Location: index.php");
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - FarmaPlus</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body class="body-login">

    <div class="contenedor-login">
        <div class="login-header">
            <h2>+ FarmaPlus</h2>
            <p>Sistema de Gestión de Farmacia</p>
        </div>

        <?php if ($error): ?>
            <div class="alerta alerta-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="grupo-form">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required autocomplete="username" placeholder="Ej. admin">
            </div>

            <div class="grupo-form">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primario" style="width: 100%; margin-top: 10px;">Ingresar al Sistema</button>
        </form>

        <div class="credenciales-demo">
            <strong>Credenciales para Pruebas (2 Roles):</strong><br>
            • Admin: <code>admin</code> / <code>123456</code><br>
            • Farmacéutico: <code>farmaceutico</code> / <code>123456</code>
        </div>
    </div>

</body>
</html>
