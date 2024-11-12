<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_pedido']) || !isset($data['estado'])) {
        throw new Exception('Datos incompletos');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Actualizar estado del pedido
    $stmt = $conn->prepare("
        UPDATE pedidos 
        SET estado = :estado
        WHERE id = :id_pedido
    ");

    $stmt->execute([
        ':estado' => $data['estado'],
        ':id_pedido' => $data['id_pedido']
    ]);

    echo json_encode([
        'success' => true,
        'mensaje' => 'Estado actualizado correctamente'
    ]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>