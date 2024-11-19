<?php
header('Content-Type: application/json');

try {
    // Conexión a la base de datos
    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Decodificar la entrada JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar que la entrada tenga los datos necesarios
    if (!isset($input['id_proveedor'], $input['productos']) || !is_array($input['productos'])) {
        throw new Exception('Datos incompletos o inválidos');
    }

    // Asignar estado (predeterminado: "pendiente")
    $estado = $input['estado'] ?? 'pendiente';

    // Modo edición o creación
    if (isset($input['id']) && !empty($input['id'])) {
        // Edición: Actualizar pedido existente
        $stmt = $conn->prepare("UPDATE pedidos_proveedores SET id_proveedor = ?, estado = ? WHERE id = ?");
        $stmt->execute([$input['id_proveedor'], $estado, $input['id']]);
    } else {
        // Creación: Insertar un nuevo pedido
        $stmt = $conn->prepare("INSERT INTO pedidos_proveedores (id_proveedor, estado, total) VALUES (?, ?, 0)");
        $stmt->execute([$input['id_proveedor'], $estado]);
        $input['id'] = $conn->lastInsertId(); // Obtener el ID del nuevo pedido
    }

    // Eliminar productos asociados al pedido anterior (si existen)
    $stmt = $conn->prepare("DELETE FROM detalle_pedidos WHERE id_pedido = ?");
    $stmt->execute([$input['id']]);

    // Calcular el nuevo total del pedido
    $total = 0;

    // Insertar los nuevos productos
    $stmt = $conn->prepare("INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    foreach ($input['productos'] as $producto) {
        if (!isset($producto['id_producto'], $producto['cantidad'], $producto['precio_unitario'])) {
            throw new Exception('Datos del producto incompletos');
        }

        // Insertar el producto en la tabla detalle_pedidos
        $stmt->execute([
            $input['id'],
            $producto['id_producto'],
            $producto['cantidad'],
            $producto['precio_unitario']
        ]);

        // Sumar al total
        $total += $producto['cantidad'] * $producto['precio_unitario'];
    }

    // Actualizar el total en la tabla pedidos_proveedores
    $stmt = $conn->prepare("UPDATE pedidos_proveedores SET total = ? WHERE id = ?");
    $stmt->execute([$total, $input['id']]);

    // Responder con éxito
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Responder con un mensaje de error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
