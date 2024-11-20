<?php
session_start();
require_once '../Admin/DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['txtID'] ?? null;
    $nombre = $_POST['txtNombre'] ?? '';
    $email = $_POST['txtEmail'] ?? '';
    $password = $_POST['txtPassword'] ?? ''; // Contraseña opcional
    $rol = $_POST['txtTipoUsuario'] ?? ''; // Cambiado a "rol"

    try {
        $db = getDB(); // Conexión a la base de datos

        // Validar datos obligatorios
        if (empty($id) || empty($nombre) || empty($email) || empty($rol)) {
            throw new Exception("Todos los campos excepto la contraseña son obligatorios.");
        }

        // Determinar si se debe actualizar la contraseña
        if (!empty($password)) {
            // Encriptar la nueva contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Preparar consulta para actualizar todo, incluida la contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, contraseña = ?, rol = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $db->error);
            }
            $stmt->bind_param("ssssi", $nombre, $email, $hashedPassword, $rol, $id);
        } else {
            // Preparar consulta para actualizar sin cambiar la contraseña
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $db->error);
            }
            $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
        }

        // Ejecutar la consulta
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Redirigir con mensaje de éxito
                header('Location: editar.php?success=Usuario actualizado correctamente');
                exit;
            } else {
                // Redirigir si no hubo cambios
                header('Location: usuarios.php?info=No se realizaron cambios en el usuario.');
                exit;
            }
        } else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redirigir con mensaje de error
        header('Location: usuarios.php?error=' . urlencode("Error al actualizar el usuario: " . $e->getMessage()));
        exit;
    }
} else {
    // Si no es POST, redirigir al listado de usuarios
    header('Location: usuarios.php?error=' . urlencode("Método de solicitud no permitido."));
    exit;
}
?>
