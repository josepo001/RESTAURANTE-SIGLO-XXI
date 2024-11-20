<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../Admin/DB.php'; // Asegúrate de que la ruta sea correcta

$mensaje = ''; // Variable para el mensaje

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $db = getDB(); // Obtener conexión a la base de datos

    // Procesar formulario si se envía
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['txtID'] ?? null;
        $nombre = $_POST['txtNombre'] ?? '';
        $email = $_POST['txtEmail'] ?? '';
        $password = $_POST['txtPassword'] ?? ''; // Contraseña opcional
        $rol = $_POST['txtTipoUsuario'] ?? '';

        // Validar datos
        if (empty($id) || empty($nombre) || empty($email) || empty($rol)) {
            $mensaje = '<div class="mensaje-error">Todos los campos excepto la contraseña son obligatorios.</div>';
        } else {
            try {
                // Determinar si se debe actualizar la contraseña
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, contraseña = ?, rol = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $nombre, $email, $hashedPassword, $rol, $id);
                } else {
                    $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
                }

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $mensaje = '<div class="mensaje-exito">Usuario actualizado correctamente.</div>';
                    } else {
                        $mensaje = '<div class="mensaje-info">No se realizaron cambios en el usuario.</div>';
                    }
                } else {
                    $mensaje = '<div class="mensaje-error">Error al actualizar el usuario.</div>';
                }
            } catch (Exception $e) {
                $mensaje = '<div class="mensaje-error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }

    // Cargar datos del usuario si no se ha enviado el formulario
    if (isset($_GET['id']) && empty($_POST)) {
        $id = $_GET['id'];
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $user = $resultado->fetch_assoc(); // Cargar los datos del usuario a editar
        } else {
            $mensaje = "<div class='mensaje-error'>Usuario no encontrado.</div>";
        }
    }
} catch (Exception $e) {
    $mensaje = "<div class='mensaje-error'>Error al obtener información del usuario: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/editar.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="profile-form">
    <h1>Editar Usuario</h1>

    <!-- Mostrar mensaje -->
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <!-- FORMULARIO -->
    <form action="" method="post">
        <input type="hidden" value="<?php echo htmlspecialchars($user['id'] ?? ''); ?>" name="txtID">        

        <div class="form-group">
            <label for="inputNombre"><b>Nombre</b></label>
            <input id="inputNombre" class="form-control" type="text" value="<?php echo htmlspecialchars($user['nombre'] ?? ''); ?>" name="txtNombre" required>
        </div>

        <div class="form-group">
            <label for="inputEmail"><b>Email</b></label>
            <input id="inputEmail" class="form-control" type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" name="txtEmail" required>
        </div>

        <div class="form-group">
            <label for="inputPassword"><b>Contraseña</b></label>
            <input id="inputPassword" class="form-control" type="password" name="txtPassword" placeholder="Dejar vacío si no desea cambiar">
        </div>

        <div class="form-group">
            <label for="tipoUsuario"><b>Tipo de Usuario</b></label>
            <select name="txtTipoUsuario" id="tipoUsuario">
                <option value="administrador" <?php echo ($user['rol'] ?? '') === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                <option value="bodega" <?php echo ($user['rol'] ?? '') === 'bodega' ? 'selected' : ''; ?>>Bodega</option>
                <option value="cocina" <?php echo ($user['rol'] ?? '') === 'cocina' ? 'selected' : ''; ?>>Cocina</option>
                <option value="finanzas" <?php echo ($user['rol'] ?? '') === 'finanzas' ? 'selected' : ''; ?>>Finanzas</option>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" name="accion" class="btn-actualizar">Actualizar</button>
            <a class="btn btn-danger btn-cancelar" href="usuarios.php">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
