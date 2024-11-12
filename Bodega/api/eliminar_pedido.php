<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('ID de pedido no proporcionado');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();
    
    // Primero eliminar los detalles del pedido
    $stmt = $conn->prepare("DELETE FROM detalle_pedidos WHERE id_pedido = :id");
    $stmt->execute([':id' => $data['id']]);

    // Luego eliminar el pedido
    $stmt = $conn->prepare("DELETE FROM pedidos_proveedores WHERE id = :id");
    $stmt->execute([':id' => $data['id']]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pedido eliminado correctamente'
    ]);

} catch(Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>