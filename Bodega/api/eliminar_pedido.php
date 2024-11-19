<?php
header('Content-Type: application/json');

try {
    // Conexión a la base de datos
    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Leer los datos enviados en la solicitud (esperando el ID del pedido)
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar que el ID del pedido sea proporcionado
    if (!isset($input['id']) || empty($input['id'])) {
        throw new Exception('ID de pedido no proporcionado');
    }

    // Eliminar primero los productos relacionados en la tabla detalle_pedidos
    $stmt = $conn->prepare("DELETE FROM detalle_pedidos WHERE id_pedido = ?");
    $stmt->execute([$input['id']]);

    // Luego eliminar el pedido en la tabla pedidos_proveedores
    $stmt = $conn->prepare("DELETE FROM pedidos_proveedores WHERE id = ?");
    $stmt->execute([$input['id']]);

    // Respuesta exitosa
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Manejar errores y responder con mensaje
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
