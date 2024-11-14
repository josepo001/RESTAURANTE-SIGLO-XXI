<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['nombreCliente']) || empty($data['nombreCliente']) || 
        !isset($data['items']) || empty($data['items']) || 
        !isset($data['id_mesa'])) {
        throw new Exception('Datos incompletos');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();

    // Verificar mesa disponible
    $stmtMesa = $conn->prepare("SELECT estado FROM mesas WHERE id = ?");
    $stmtMesa->execute([$data['id_mesa']]);
    $mesa = $stmtMesa->fetch(PDO::FETCH_ASSOC);

    if (!$mesa || $mesa['estado'] !== 'disponible') {
        throw new Exception('Mesa no disponible');
    }

    // Crear pedido (reemplazar `id_usuario` por `nombre_cliente`)
    $stmt = $conn->prepare("
        INSERT INTO pedidos (id_mesa, nombre_cliente, fecha_pedido, estado, total)
        VALUES (:id_mesa, :nombreCliente, NOW(), 'pendiente', :total)
    ");

    $stmt->execute([
        ':id_mesa' => $data['id_mesa'],
        ':nombreCliente' => $data['nombreCliente'],
        ':total' => $data['total']
    ]);

    $idPedido = $conn->lastInsertId();

    // Registrar en finanzas
    $stmtFinanzas = $conn->prepare("
        INSERT INTO finanzas (fecha, ingresos, egresos, id_pedido, tipo_transaccion)
        VALUES (CURDATE(), :total, 0, :idPedido, 'ingreso_venta')
    ");
    
    $stmtFinanzas->execute([
        ':total' => $data['total'],
        ':idPedido' => $idPedido
    ]);

    // Insertar detalles del pedido
    $stmtDetalle = $conn->prepare("
        INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio_unitario)
        VALUES (:idPedido, :idProducto, :cantidad, :precioUnitario)
    ");

    foreach ($data['items'] as $item) {
        $stmtDetalle->execute([
            ':idPedido' => $idPedido,
            ':idProducto' => $item['id'],
            ':cantidad' => $item['cantidad'],
            ':precioUnitario' => $item['precio']
        ]);
    }

    // Actualizar estado de la mesa
    $stmtUpdateMesa = $conn->prepare("UPDATE mesas SET estado = 'ocupada' WHERE id = ?");
    $stmtUpdateMesa->execute([$data['id_mesa']]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'idPedido' => $idPedido,
        'mensaje' => 'Pedido creado exitosamente'
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
