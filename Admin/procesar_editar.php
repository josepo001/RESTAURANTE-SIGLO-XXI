<?php
require_once '../Admin/DB.php'; // Asegúrate de que la ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['txtID'];
    $nombre = $_POST['txtNombre'];
    $email = $_POST['txtEmail'];
    $password = $_POST['txtPassword'];
    $rol = $_POST['txtTipoUsuario'];

    try {
        $db = getDB(); // Obtener conexión a la base de datos

        // Actualizar el usuario con o sin contraseña
        if (!empty($password)) {
            // Si se proporciona una nueva contraseña, encriptarla y actualizar
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, contraseña = ?, rol = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("ssssi", $nombre, $email, $hashedPassword, $rol, $id);
        } else {
            // Si no se proporciona una nueva contraseña, actualizar sin cambiar la contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }
            $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
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
