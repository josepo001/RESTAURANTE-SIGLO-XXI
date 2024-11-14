<?php
require_once '../Admin/DB.php'; // Asegúrate de que la ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $numero = $_POST['numero'];
    $capacidad = $_POST['capacidad'];
    $estado = $_POST['estado'];

    try {
        $db = getDB(); // Obtener conexión a la base de datos

        // Preparar la consulta para actualizar la mesa
        $stmt = $db->prepare("UPDATE mesas SET numero = ?, capacidad = ?, estado = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $db->error);
        }

        $stmt->bind_param("sisi", $numero, $capacidad, $estado, $id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Redirigir con mensaje de éxito
            header('Location: mesas.php?success=Mesa actualizada correctamente');
            exit;
        } else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        // Redirigir con mensaje de error
        header('Location: mesas.php?error=' . urlencode("Error al actualizar la mesa: " . $e->getMessage()));
        exit;
    }
} else {
    // Redirigir si no se envía mediante POST
    header('Location: mesas.php?error=' . urlencode("Método de solicitud no permitido"));
    exit;
}
