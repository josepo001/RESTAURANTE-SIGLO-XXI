<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener recetas con nombres de productos e ingredientes
    $sql = "
        SELECT 
            r.id,
            r.id_producto,
            r.id_ingrediente,
            r.cantidad,
            p.nombre as nombre_producto,
            i.nombre as nombre_ingrediente,
            i.unidad,
            i.stock as stock_actual,
            i.stock_minimo
        FROM recetas r
        JOIN productos p ON r.id_producto = p.id
        JOIN ingredientes i ON r.id_ingrediente = i.id
        ORDER BY p.nombre, i.nombre
    ";

    $stmt = $pdo->query($sql);
    $recetas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organizar los datos por producto
    $recetasAgrupadas = [];
    foreach ($recetas as $receta) {
        $id_producto = $receta['id_producto'];
        if (!isset($recetasAgrupadas[$id_producto])) {
            $recetasAgrupadas[$id_producto] = [
                'id_producto' => $id_producto,
                'nombre_producto' => $receta['nombre_producto'],
                'ingredientes' => []
            ];
        }
        $recetasAgrupadas[$id_producto]['ingredientes'][] = [
            'id' => $receta['id_ingrediente'],
            'nombre' => $receta['nombre_ingrediente'],
            'cantidad' => $receta['cantidad'],
            'unidad' => $receta['unidad'],
            'stock_actual' => $receta['stock_actual'],
            'stock_minimo' => $receta['stock_minimo']
        ];
    }

    echo json_encode([
        'success' => true,
        'recetas' => array_values($recetasAgrupadas)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener recetas: ' . $e->getMessage()
    ]);
}
?>