<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once '../config/database.php';

    // Verificar que el método sea DELETE
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Método no permitido');
    }

    // Verificar que el ID esté presente en la URL
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('El ID del producto es requerido y debe ser numérico');
    }

    $id = intval($_GET['id']);

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el producto existe antes de eliminar
    $stmt = $pdo->prepare("SELECT id FROM ingredientes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        throw new Exception('El producto con el ID especificado no existe');
    }

    // Eliminar el producto
    $stmt = $pdo->prepare("DELETE FROM ingredientes WHERE id = :id");
    $resultado = $stmt->execute([':id' => $id]);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Producto eliminado correctamente'
        ]);
    } else {
        throw new Exception('Error al eliminar el producto');
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
