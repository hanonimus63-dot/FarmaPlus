<?php
// registro.php - Formulario público de registro de nuevos usuarios
require_once 'config.php';

// Si ya está autenticado, redirigir al panel principal
if (estaAutenticado()) {
    header("Location: index.php");
    exit();
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $rol = $_POST['rol'] ?? 'farmaceutico';

    if (empty($nombre) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor complete todos los campos obligatorios.';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 4) {
        $error = 'La contraseña debe tener al menos 4 caracteres.';
    } else {
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'El nombre de usuario ya está registrado. Por favor elija otro.';
        } else {
            try {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_ins = $pdo->prepare("INSERT INTO usuarios (username, password, nombre, rol) VALUES (?, ?, ?, ?)");
                $stmt_ins->execute([$username, $password_hash, $nombre, $rol]);

                $exito = '¡Cuenta creada con éxito! Ya puedes iniciar sesión.';
            } catch (PDOException $e) {
                $error = 'Error al registrar la cuenta en la base de datos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - FarmaPlus</title>
    <style>
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

        .contenedor-registro {
            background-color: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: 1px solid #dcdcdc;
        }

        .registro-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .registro-header h2 {
            color: #0056b3;
            font-size: 26px;
            margin-bottom: 5px;
        }

        .registro-header p {
            color: #666666;
            font-size: 14px;
        }

        .grupo-form {
            margin-bottom: 15px;
        }

        .grupo-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
            color: #333333;
        }

        .grupo-form input, .grupo-form select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }

        .grupo-form input:focus, .grupo-form select:focus {
            border-color: #0056b3;
        }

        .btn-registrar {
            width: 100%;
            background-color: #28a745;
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

        .btn-registrar:hover {
            background-color: #218838;
        }

        .alerta-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .alerta-exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .enlace-login {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .enlace-login a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }

        .enlace-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="contenedor-registro">
        <div class="registro-header">
            <h2>+ FarmaPlus</h2>
            <p>Crear Nueva Cuenta de Usuario</p>
        </div>

        <?php if ($error): ?>
            <div class="alerta-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="alerta-exito">
                <?= htmlspecialchars($exito) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="registro.php">
            <div class="grupo-form">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Ej. Ashley Moreta" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            </div>

            <div class="grupo-form">
                <label for="username">Usuario (Login) *</label>
                <input type="text" id="username" name="username" required placeholder="Ej. amoreta" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="grupo-form">
                <label for="rol">Rol en la Farmacia *</label>
                <select id="rol" name="rol" required>
                    <option value="farmaceutico">Farmacéutico</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <div class="grupo-form">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="grupo-form">
                <label for="confirm_password">Confirmar Contraseña *</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-registrar">Crear Cuenta</button>
        </form>

        <div class="enlace-login">
            ¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión aquí</a>
        </div>
    </div>

</body>
</html>
