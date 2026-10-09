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
    <style>
        /* Estilos propios e independientes para login.php */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .contenedor-login {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: 1px solid #dcdcdc;
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header h2 {
            color: #0056b3;
            font-size: 26px;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #666666;
            font-size: 14px;
        }

        .grupo-form {
            margin-bottom: 18px;
        }

        .grupo-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #333333;
        }

        .grupo-form input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .grupo-form input:focus {
            border-color: #0056b3;
        }

        .btn-ingresar {
            width: 100%;
            background-color: #0056b3;
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 4px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 5px;
        }

        .btn-ingresar:hover {
            background-color: #004085;
        }

        .alerta-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        .credenciales-demo {
            background-color: #f8f9fa;
            border: 1px dashed #cccccc;
            padding: 12px;
            margin-top: 20px;
            font-size: 12px;
            border-radius: 4px;
            color: #495057;
            line-height: 1.5;
        }

        .credenciales-demo code {
            background-color: #e9ecef;
            padding: 2px 5px;
            border-radius: 3px;
            color: #d63384;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="contenedor-login">
        <div class="login-header">
            <h2>+ FarmaPlus</h2>
            <p>Sistema de Gestión de Farmacia</p>
        </div>

        <?php if ($error): ?>
            <div class="alerta-error">
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

            <button type="submit" class="btn-ingresar">Ingresar al Sistema</button>
        </form>

        <div class="credenciales-demo">
            <strong>Credenciales para Pruebas (2 Roles):</strong><br>
            • Admin: <code>admin</code> / <code>123456</code><br>
            • Farmacéutico: <code>farmaceutico</code> / <code>123456</code>
        </div>
    </div>

</body>
</html>
