<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener total de productos
    $stmt = $pdo->query("SELECT COUNT(*) FROM ingredientes");
    $totalProductos = $stmt->fetchColumn();

    // Obtener productos bajos en stock
    $stmt = $pdo->query("SELECT COUNT(*) FROM ingredientes WHERE stock <= stock_minimo");
    $productosBajos = $stmt->fetchColumn();

    // Obtener pedidos pendientes
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos_proveedores WHERE estado = 'pendiente'");
    $pedidosPendientes = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'totalProductos' => $totalProductos,
        'productosBajos' => $productosBajos,
        'pedidosPendientes' => $pedidosPendientes
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener resumen: ' . $e->getMessage()
    ]);
}