<?php
header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID de pedido no proporcionado');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el pedido
    $stmt = $conn->prepare("
        SELECT 
            pp.*,
            p.nombre as proveedor_nombre,
            p.contacto,
            p.telefono
        FROM pedidos_proveedores pp
        LEFT JOIN proveedores p ON pp.id_proveedor = p.id
        WHERE pp.id = :id
    ");
    $stmt->execute([':id' => $_GET['id']]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        throw new Exception('Pedido no encontrado');
    }

    // Obtener los productos del pedido
    $stmt = $conn->prepare("
        SELECT 
            dp.*,
            i.nombre as nombre_producto,
            i.unidad
        FROM detalle_pedidos dp
        LEFT JOIN ingredientes i ON dp.id_producto = i.id
        WHERE dp.id_pedido = :id_pedido
    ");
    $stmt->execute([':id_pedido' => $_GET['id']]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pedido['productos'] = array_map(function($producto) {
        return [
            'id_producto' => $producto['id_producto'],
            'nombre' => $producto['nombre_producto'],
            'cantidad' => $producto['cantidad'],
            'precio' => $producto['precio_unitario'],
            'unidad' => $producto['unidad']
        ];
    }, $productos);

    echo json_encode([
        'success' => true,
        'pedido' => $pedido
    ]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>