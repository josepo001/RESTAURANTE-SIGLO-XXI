<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once '../config/database.php';

    // Leer los datos del cuerpo de la solicitud (JSON)
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar que todos los campos necesarios estén presentes
    if (!isset($data['nombre']) || !isset($data['stock']) || !isset($data['stock_minimo']) || !isset($data['unidad'])) {
        throw new Exception('Faltan datos requeridos');
    }

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sanitizar y validar datos
    $nombre = trim($data['nombre']);
    $stock = intval($data['stock']);
    $unidad = trim($data['unidad']);
    $stock_minimo = intval($data['stock_minimo']);

    // Verificar valores válidos
    if (empty($nombre) || $stock < 0 || $stock_minimo < 0) {
        throw new Exception('Valores inválidos');
    }

    // Verificar si el producto existe
    $stmt = $pdo->prepare("SELECT id FROM ingredientes WHERE nombre = :nombre");
    $stmt->execute([':nombre' => $nombre]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);

    $pdo->beginTransaction();

    try {
        if ($existe) {
            // Actualizar (sin la columna "cantidad")
            $sql = "UPDATE ingredientes 
                    SET stock = :stock,
                        unidad = :unidad,
                        stock_minimo = :stock_minimo
                    WHERE nombre = :nombre";
        } else {
            // Insertar nuevo (sin la columna "cantidad")
            $sql = "INSERT INTO ingredientes (nombre, stock, unidad, stock_minimo) 
                    VALUES (:nombre, :stock, :unidad, :stock_minimo)";
        }

        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            ':nombre' => $nombre,
            ':stock' => $stock,
            ':unidad' => $unidad,
            ':stock_minimo' => $stock_minimo
        ]);

        if ($resultado) {
            $pdo->commit();
            echo json_encode([
                'success' => true,
                'message' => $existe ? 'Producto actualizado correctamente' : 'Producto guardado correctamente',
                'data' => [
                    'nombre' => $nombre,
                    'stock' => $stock,
                    'unidad' => $unidad,
                    'stock_minimo' => $stock_minimo
                ]
            ]);
        } else {
            throw new Exception('Error al ejecutar la consulta');
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
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
