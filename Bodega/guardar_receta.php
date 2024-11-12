<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener datos del POST
    $formData = json_decode(file_get_contents('php://input'), true);
    
    // Log para debugging
    error_log('Datos recibidos: ' . print_r($formData, true));

    // Validar datos recibidos
    if (!isset($formData['id_producto']) || empty($formData['ingredientes'])) {
        throw new Exception('Faltan datos requeridos para la receta');
    }

    $pdo->beginTransaction();

    try {
        // Primero eliminar la receta existente si existe
        $stmt = $pdo->prepare("DELETE FROM recetas WHERE id_producto = ?");
        $stmt->execute([$formData['id_producto']]);

        // Preparar la consulta para insertar ingredientes
        $stmt = $pdo->prepare("
            INSERT INTO recetas (id_producto, id_ingrediente, cantidad) 
            VALUES (:id_producto, :id_ingrediente, :cantidad)
        ");

        // Insertar cada ingrediente
        foreach ($formData['ingredientes'] as $ingrediente) {
            if (!isset($ingrediente['id']) || !isset($ingrediente['cantidad'])) {
                throw new Exception('Datos de ingrediente incompletos');
            }

            $resultado = $stmt->execute([
                ':id_producto' => $formData['id_producto'],
                ':id_ingrediente' => $ingrediente['id'],
                ':cantidad' => $ingrediente['cantidad']
            ]);

            if (!$resultado) {
                throw new Exception('Error al insertar ingrediente');
            }
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Receta guardada correctamente'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Error en guardar_receta.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la receta: ' . $e->getMessage()
    ]);
}