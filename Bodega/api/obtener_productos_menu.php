<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener únicamente ID y nombre
    $stmt = $pdo->query("SELECT id, nombre, descripcion, categoria FROM productos ORDER BY nombre");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'products' => $productos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener productos del menú: ' . $e->getMessage()
    ]);
}
?>
