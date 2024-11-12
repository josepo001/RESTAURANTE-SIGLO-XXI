<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener todas las recetas con sus ingredientes
    $sql = "
        SELECT 
            p.id as producto_id,
            p.nombre as producto_nombre,
            r.id_ingrediente,
            r.cantidad as cantidad_necesaria,
            i.nombre as ingrediente_nombre,
            i.stock as stock_actual
        FROM productos p
        JOIN recetas r ON p.id = r.id_producto
        JOIN ingredientes i ON r.id_ingrediente = i.id
    ";

    $stmt = $pdo->query($sql);
    $recetas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar por producto y verificar stock
    $disponibilidad = [];
    foreach ($recetas as $receta) {
        $id = $receta['producto_id'];
        if (!isset($disponibilidad[$id])) {
            $disponibilidad[$id] = [
                'id' => $id,
                'nombre' => $receta['producto_nombre'],
                'disponible' => true
            ];
        }

        // Verificar si hay suficiente stock
        if ($receta['stock_actual'] < $receta['cantidad_necesaria']) {
            $disponibilidad[$id]['disponible'] = false;
        }
    }

    echo json_encode([
        'success' => true,
        'disponibilidad' => array_values($disponibilidad)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar stock: ' . $e->getMessage()
    ]);
}
?>