<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

try {
    // Verificar que los datos necesarios estén presentes
    if (!isset($_POST['id']) || !isset($_POST['stock'])) {
        throw new Exception('Faltan datos requeridos');
    }

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = intval($_POST['id']);
    $stock = intval($_POST['stock']);

    // Validaciones
    if ($stock < 0) {
        throw new Exception('El stock no puede ser negativo');
    }

    if ($id <= 0) {
        throw new Exception('ID de producto inválido');
    }

    // Verificar que el producto existe
    $stmt = $pdo->prepare("SELECT id FROM ingredientes WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        throw new Exception('Producto no encontrado');
    }

    // Actualizar el stock
    $stmt = $pdo->prepare("
        UPDATE ingredientes 
        SET stock = :stock, 
            cantidad = :stock 
        WHERE id = :id
    ");

    $resultado = $stmt->execute([
        ':stock' => $stock,
        ':id' => $id
    ]);

    if ($resultado) {
        // Obtener el producto actualizado
        $stmt = $pdo->prepare("SELECT * FROM ingredientes WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => 'Stock actualizado correctamente',
            'producto' => $producto
        ]);
    } else {
        throw new Exception('Error al actualizar el stock');
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>