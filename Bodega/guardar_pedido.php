<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_proveedor']) || !isset($data['items'])) {
        throw new Exception('Faltan datos requeridos');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();

    // Insertar pedido
    $stmt = $conn->prepare("
        INSERT INTO pedidos_proveedores (id_proveedor, fecha_pedido, estado, total)
        VALUES (:id_proveedor, NOW(), 'pendiente', :total)
    ");

    $total = array_sum(array_map(function($item) {
        return $item['cantidad'] * $item['precio'];
    }, $data['items']));

    $stmt->execute([
        ':id_proveedor' => $data['id_proveedor'],
        ':total' => $total
    ]);

    $idPedido = $conn->lastInsertId();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'idPedido' => $idPedido,
        'mensaje' => 'Pedido guardado correctamente'
    ]);

} catch(Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>