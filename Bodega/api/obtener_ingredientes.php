<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener todos los ingredientes
    $sql = "SELECT id, nombre, unidad, stock FROM ingredientes ORDER BY nombre";
    $stmt = $pdo->query($sql);
    $ingredientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'ingredientes' => $ingredientes
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener ingredientes: ' . $e->getMessage()
    ]);
}
?>
