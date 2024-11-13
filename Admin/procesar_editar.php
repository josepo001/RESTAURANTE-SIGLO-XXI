<?php
require_once '../Admin/DB.php'; // Asegúrate de que la ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['txtID'];
    $nombre = $_POST['txtNombre'];
    $apellido = $_POST['txtApellido'];
    $email = $_POST['txtEmail'];
    $password = $_POST['txtPassword'];
    $tipo_usuario = $_POST['txtTipoUsuario'];

    try {
        $db = getDB(); // Obtener conexión a la base de datos

        // Actualizar el usuario con o sin contraseña
        if (!empty($password)) {
            // Si se proporciona una nueva contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, contraseña = ?, tipo_usuario = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $nombre, $apellido, $email, $hashedPassword, $tipo_usuario, $id);
        } else {
            // Si no se proporciona una nueva contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, email = ?, tipo_usuario = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }
            $stmt->bind_param("ssssi", $nombre, $apellido, $email, $tipo_usuario, $id);
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir con mensaje de éxito
            header('Location: usuarios.php?success=Usuario actualizado correctamente');
            exit;
        } else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redirigir con mensaje de error
        header('Location: usuarios.php?error=' . urlencode("Error al actualizar el usuario: " . $e->getMessage()));
        exit;
    }
} else {
    // Redirigir si no se envía mediante POST
    header('Location: usuarios.php?error=' . urlencode("Método de solicitud no permitido"));
    exit;
}
?>
