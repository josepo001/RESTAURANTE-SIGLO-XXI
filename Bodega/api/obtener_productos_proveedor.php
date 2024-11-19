<?php
header('Content-Type: application/json');

try {
    if (!isset($_GET['id_proveedor'])) {
        throw new Exception('ID del proveedor no proporcionado.');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("
        SELECT p.id, p.nombre, p.unidad, pp.precio_unitario
        FROM productos_proveedores pp
        JOIN ingredientes p ON pp.id_producto = p.id
        WHERE pp.id_proveedor = :id_proveedor
    ");
    $stmt->execute([":id_proveedor" => $_GET['id_proveedor']]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "productos" => $productos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al obtener productos: " . $e->getMessage()
    ]);
}
?>
