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
    <link rel="stylesheet" href="css/estilos.css?v=1.1">
    <style>
        /* CSS embebido de respaldo para garantizar renderizado en Railway */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body.body-login {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .contenedor-login {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border: 1px solid #dcdcdc;
        }
        .login-header { text-align: center; margin-bottom: 20px; }
        .login-header h2 { color: #0056b3; margin-bottom: 5px; font-size: 24px; }
        .login-header p { color: #666; font-size: 14px; }
        .grupo-form { margin-bottom: 15px; }
        .grupo-form label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        .grupo-form input {
            width: 100%; padding: 8px 10px; border: 1px solid #cccccc; border-radius: 4px; font-size: 14px;
        }
        .btn {
            display: inline-block; padding: 10px 14px; border: none; border-radius: 4px; cursor: pointer;
            font-size: 14px; font-weight: bold; text-decoration: none; text-align: center;
        }
        .btn-primario { background-color: #0056b3; color: white; }
        .btn-primario:hover { background-color: #004085; }
        .credenciales-demo {
            background-color: #f8f9fa; border: 1px dashed #cccccc; padding: 10px; margin-top: 15px;
            font-size: 12px; border-radius: 4px; color: #495057; line-height: 1.5;
        }
        .alerta-error {
            background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
            padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 14px;
        }
    </style>
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
