<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'DB.php'; // Asegúrate de que la ruta sea correcta

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener conexión a la base de datos
    $db = getDB();

    // Capturar los datos del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar la contraseña

    try {
        // Validar si el email ya existe en la base de datos
        $checkEmailStmt = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        if (!$checkEmailStmt) {
            throw new Exception("Error en la consulta de verificación de email: " . $db->error);
        }
        $checkEmailStmt->bind_param("s", $email);
        $checkEmailStmt->execute();
        $checkEmailStmt->bind_result($count);
        $checkEmailStmt->fetch();
        $checkEmailStmt->close();

        if ($count > 0) {
            throw new Exception("El email ya está en uso.");
        }

        // Insertar nuevo usuario en la base de datos
        $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error en la consulta de inserción de usuario: " . $db->error);
        }
        $stmt->bind_param("ssss", $nombre, $email, $password, $rol);
        $stmt->execute();

        // Redirigir con mensaje de éxito
        header('Location: usuarios.php?success=Usuario registrado correctamente');
        exit;

    } catch (Exception $e) {
        $mensaje = "Error al registrar usuario: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="../css/registrar.css">
</head>
<body>
    <header class="header">
        <h1>Registrar Nuevo Usuario</h1>
    </header>

    <div class="position-absolute top-0 start-0 p-3">
    <a href="usuarios.php" class="btn-volver">Volver</a>

    </div>

    <?php if ($mensaje): ?>
        <div class="error-message">
            <p><?php echo $mensaje; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="administrador">Administrador</option>
                <option value="cocina">Cocina</option>
                <option value="bodega">Bodega</option>
                <option value="finanzas">Finanzas</option>
            </select>
        </div>
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Registrar Usuario</button>
    </form>
</body>
</html>
