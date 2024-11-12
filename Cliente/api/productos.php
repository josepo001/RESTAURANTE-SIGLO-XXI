<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para verificar disponibilidad basada en recetas e ingredientes
    $sql = "
        SELECT 
            p.*,
            CASE 
                WHEN r.id_producto IS NULL THEN 1
                WHEN MIN(i.stock) >= MIN(r.cantidad) THEN 1
                ELSE 0
            END as disponible
        FROM productos p
        LEFT JOIN recetas r ON p.id = r.id_producto
        LEFT JOIN ingredientes i ON r.id_ingrediente = i.id
        GROUP BY p.id
        ORDER BY p.categoria, p.nombre
    ";

    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'productos' => array_map(function($producto) {
            return [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'descripcion' => $producto['descripcion'],
                'categoria' => $producto['categoria'],
                'imagen' => $producto['imagen'],
                'precio' => $producto['precio'],
                'disponible' => (bool)$producto['disponible']
            ];
        }, $productos)
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>