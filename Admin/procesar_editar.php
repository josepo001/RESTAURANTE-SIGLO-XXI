<?php
session_start();
require_once '../Admin/DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id']; // ID del usuario enviado desde el formulario
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? ''; // Contraseña opcional

    try {
        $db = getDB(); // Conexión a la base de datos

        // Validar campos obligatorios
        if (empty($nombre) || empty($email)) {
            throw new Exception("Nombre y correo son campos obligatorios.");
        }

        // Determinar si se proporcionó una nueva contraseña
        if (!empty($password)) {
            // Encriptar la nueva contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Preparar consulta para actualizar incluyendo la contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, contraseña = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }
            $stmt->bind_param("sssi", $nombre, $email, $hashedPassword, $userId);
        } else {
            // Preparar consulta para actualizar sin cambiar la contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $db->error);
            }
            $stmt->bind_param("ssi", $nombre, $email, $userId);
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Redirigir con mensaje de éxito si hubo cambios
                header('Location: perfilAdmin.php?success=Perfil actualizado correctamente');
            } else {
                // Redirigir con mensaje si no hubo cambios
                header('Location: perfilAdmin.php?error=No se realizaron cambios en el perfil');
            }
            exit;
        } else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redirigir con mensaje de error
        header('Location: perfilAdmin.php?error=' . urlencode("Error al actualizar el perfil: " . $e->getMessage()));
        exit;
    }
} else {
    // Si no es una solicitud POST, redirigir al formulario
    header('Location: perfilAdmin.php?error=' . urlencode("Método de solicitud no permitido."));
    exit;
}
?>
