<?php
header('Content-Type: application/json');

try {
    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "
        SELECT 
            pp.*,
            p.nombre as proveedor_nombre,
            p.contacto,
            p.telefono,
            i.nombre as producto_nombre,
            pp_i.cantidad,
            pp_i.precio_unitario
        FROM pedidos_proveedores pp
        LEFT JOIN proveedores p ON pp.id_proveedor = p.id
        LEFT JOIN detalle_pedidos pp_i ON pp.id = pp_i.id_pedido
        LEFT JOIN ingredientes i ON pp_i.id_producto = i.id
        ORDER BY pp.fecha_pedido DESC
    ";
    
    $stmt = $conn->query($query);
    $pedidos = [];
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($pedidos[$row['id']])) {
            $pedidos[$row['id']] = [
                'id' => $row['id'],
                'proveedor_nombre' => $row['proveedor_nombre'],
                'contacto' => $row['contacto'],
                'telefono' => $row['telefono'],
                'fecha_pedido' => $row['fecha_pedido'],
                'estado' => $row['estado'],
                'total' => $row['total'],
                'productos' => []
            ];
        }
        if ($row['producto_nombre']) {
            $pedidos[$row['id']]['productos'][] = [
                'nombre' => $row['producto_nombre'],
                'cantidad' => $row['cantidad'],
                'precio_unitario' => $row['precio_unitario']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'pedidos' => array_values($pedidos)
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener pedidos: ' . $e->getMessage()
    ]);
}
?>