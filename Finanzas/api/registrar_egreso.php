<?php
header('Content-Type: application/json');

try {
    if (!isset($_POST['concepto']) || !isset($_POST['monto']) || !isset($_POST['fecha'])) {
        throw new Exception('Faltan datos requeridos');
    }

    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->beginTransaction();

    // Registrar egreso en finanzas
    $stmt = $conn->prepare("
        INSERT INTO finanzas (fecha, ingresos, egresos, tipo_transaccion)
        VALUES (:fecha, 0, :monto, 'egreso_compra')
    ");

    $stmt->execute([
        ':fecha' => $_POST['fecha'],
        ':monto' => floatval($_POST['monto'])
    ]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Egreso registrado correctamente'
    ]);

} catch(Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>