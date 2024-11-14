<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_pedido']) || !isset($data['id_mesa']) || !isset($data['total'])) {
        throw new Exception('Datos incompletos');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transacción para asegurar que ambas operaciones ocurren juntas
    $conn->beginTransaction();

    // Insertar en la tabla transacciones
    $stmt = $conn->prepare("
        INSERT INTO transacciones (id_pedido, id_mesa, total)
        VALUES (:id_pedido, :id_mesa, :total)
    ");
    $stmt->execute([
        ':id_pedido' => $data['id_pedido'],
        ':id_mesa' => $data['id_mesa'],
        ':total' => $data['total']
    ]);

    // Actualizar el estado del pedido a "pagado" y establecer el total en la tabla `pedidos`
    $stmt = $conn->prepare("
        UPDATE pedidos 
        SET estado = 'pagado', total = :total 
        WHERE id = :id_pedido
    ");
    $stmt->execute([
        ':total' => $data['total'],
        ':id_pedido' => $data['id_pedido']
    ]);

    // Confirmar transacción
    $conn->commit();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Transacción registrada y pedido actualizado a pagado correctamente'
    ]);

} catch(Exception $e) {
    // Si ocurre un error, revertir la transacción
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
