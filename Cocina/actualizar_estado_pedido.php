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

    // Verificar que el pedido existe antes de actualizar
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM pedidos WHERE id = :id_pedido");
    $checkStmt->execute([':id_pedido' => $data['id_pedido']]);
    $pedidoExiste = $checkStmt->fetchColumn();

    if ($pedidoExiste == 0) {
        throw new Exception('El pedido no existe.');
    }

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

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Estado actualizado correctamente'
        ]);
    } else {
        throw new Exception('No se pudo actualizar el estado. Verifica el ID del pedido.');
    }

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
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

    // Verificar que el pedido existe antes de actualizar
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM pedidos WHERE id = :id_pedido");
    $checkStmt->execute([':id_pedido' => $data['id_pedido']]);
    $pedidoExiste = $checkStmt->fetchColumn();

    if ($pedidoExiste == 0) {
        throw new Exception('El pedido no existe.');
    }

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

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Estado actualizado correctamente'
        ]);
    } else {
        throw new Exception('No se pudo actualizar el estado. Verifica el ID del pedido.');
    }

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
