<?php
header('Content-Type: application/json');

try {
    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener pedidos activos con sus detalles
    $stmt = $conn->prepare("
    SELECT 
        p.id,
        p.id_mesa,
        p.nombre_cliente,
        p.estado,
        p.tiempo_estimado,
        p.fecha_pedido,
        p.total,  /* Añadir el total aquí */
        GROUP_CONCAT(
            CONCAT(
                dp.cantidad,
                '|',
                pr.nombre,
                '|',
                dp.precio_unitario
            )
        ) as items
    FROM pedidos p
    LEFT JOIN detalle_pedidos dp ON p.id = dp.id_pedido
    LEFT JOIN productos pr ON dp.id_producto = pr.id
    WHERE p.estado IN ('pendiente', 'en preparación', 'completado', 'entregado')
    GROUP BY p.id
    ORDER BY p.fecha_pedido ASC
");

    
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los items de cada pedido
    foreach ($pedidos as &$pedido) {
        $items = [];
        if ($pedido['items']) {
            $itemsArray = explode(',', $pedido['items']);
            foreach ($itemsArray as $item) {
                list($cantidad, $nombre, $precio) = explode('|', $item);
                $items[] = [
                    'cantidad' => $cantidad,
                    'nombre' => $nombre,
                    'precio' => $precio
                ];
            }
        }
        $pedido['items'] = $items;
    }

    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
