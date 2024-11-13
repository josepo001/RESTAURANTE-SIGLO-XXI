<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../Admin/DB.php'; // Asegúrate de que la ruta sea correcta

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Verificar si se ha recibido el ID del usuario para eliminar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['txtID'])) {
    $id_usuario = $_POST['txtID'];

    try {
        $db = getDB();
        
        // Iniciar transacción (por si quieres agregar más eliminaciones en el futuro)
        $db->begin_transaction();

        // Eliminar el usuario de la tabla "usuarios"
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta para usuarios: " . $db->error);
        }
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();

        // Verificar si se eliminó algún usuario
        if ($stmt->affected_rows > 0) {
            $db->commit(); // Confirmar la transacción
            header('Location: usuarios.php?success=Usuario eliminado correctamente.');
            exit;
        } else {
            $db->rollback(); // Revertir la transacción
            header('Location: usuarios.php?error=No se encontró el usuario a eliminar.');
            exit;
        }
    } catch (Exception $e) {
        $db->rollback(); // Revertir la transacción en caso de error
        error_log("Error al eliminar usuario: " . $e->getMessage()); // Registrar el error
        header('Location: usuarios.php?error=Error al eliminar el usuario.');
        exit;
    }
} else {
    header('Location: usuarios.php?error=ID de usuario no proporcionado.');
    exit;
}
?>