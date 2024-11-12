<?php
session_start();
header('Content-Type: application/json');
require_once('../../Admin/DB.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = new DB();
    $conn = $db->connect();
    
    // Iniciar transacción
    $conn->beginTransaction();
    
    // Crear el pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (id_cliente, fecha) VALUES (?, NOW())");
    $stmt->execute([$_SESSION['id_usuario']]);
    $id_pedido = $conn->lastInsertId();
    
    // Insertar detalles del pedido
    foreach ($data['items'] as $item) {
        $stmt = $conn->prepare("INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad) VALUES (?, ?, ?)");
        $stmt->execute([$id_pedido, $item['id'], $item['cantidad']]);
        
        // Actualizar stock
        $stmt = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['cantidad'], $item['id']]);
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'id_pedido' => $id_pedido]);
} catch(PDOException $e) {
    $conn->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?>