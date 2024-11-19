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
        // Consulta para verificar si la receta ya existe
        $stmtVerificar = $pdo->prepare("SELECT COUNT(*) FROM recetas WHERE id_producto = :id_producto");
        $stmtVerificar->execute([':id_producto' => $formData['id_producto']]);
        $existeReceta = $stmtVerificar->fetchColumn() > 0;

        if ($existeReceta) {
            // Actualizar receta existente
            foreach ($formData['ingredientes'] as $ingrediente) {
                if (!isset($ingrediente['id']) || !isset($ingrediente['cantidad'])) {
                    throw new Exception('Datos de ingrediente incompletos');
                }

                // Verificar si el ingrediente ya existe en la receta
                $stmtVerificarIngrediente = $pdo->prepare("
                    SELECT COUNT(*) 
                    FROM recetas 
                    WHERE id_producto = :id_producto AND id_ingrediente = :id_ingrediente
                ");
                $stmtVerificarIngrediente->execute([
                    ':id_producto' => $formData['id_producto'],
                    ':id_ingrediente' => $ingrediente['id']
                ]);

                $existeIngrediente = $stmtVerificarIngrediente->fetchColumn() > 0;

                if ($existeIngrediente) {
                    // Actualizar ingrediente existente
                    $stmtActualizar = $pdo->prepare("
                        UPDATE recetas 
                        SET cantidad = :cantidad 
                        WHERE id_producto = :id_producto AND id_ingrediente = :id_ingrediente
                    ");
                    $stmtActualizar->execute([
                        ':id_producto' => $formData['id_producto'],
                        ':id_ingrediente' => $ingrediente['id'],
                        ':cantidad' => $ingrediente['cantidad']
                    ]);
                } else {
                    // Insertar nuevo ingrediente para la receta existente
                    $stmtInsertarIngrediente = $pdo->prepare("
                        INSERT INTO recetas (id_producto, id_ingrediente, cantidad) 
                        VALUES (:id_producto, :id_ingrediente, :cantidad)
                    ");
                    $stmtInsertarIngrediente->execute([
                        ':id_producto' => $formData['id_producto'],
                        ':id_ingrediente' => $ingrediente['id'],
                        ':cantidad' => $ingrediente['cantidad']
                    ]);
                }
            }
        } else {
            // Insertar nueva receta con todos los ingredientes
            $stmtInsertar = $pdo->prepare("
                INSERT INTO recetas (id_producto, id_ingrediente, cantidad) 
                VALUES (:id_producto, :id_ingrediente, :cantidad)
            ");
            foreach ($formData['ingredientes'] as $ingrediente) {
                if (!isset($ingrediente['id']) || !isset($ingrediente['cantidad'])) {
                    throw new Exception('Datos de ingrediente incompletos');
                }

                $stmtInsertar->execute([
                    ':id_producto' => $formData['id_producto'],
                    ':id_ingrediente' => $ingrediente['id'],
                    ':cantidad' => $ingrediente['cantidad']
                ]);
            }
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => $existeReceta ? 'Receta actualizada correctamente' : 'Receta guardada correctamente'
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
