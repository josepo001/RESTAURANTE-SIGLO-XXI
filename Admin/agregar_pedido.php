<?php
// Iniciar la sesión si aún no ha comenzado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir el archivo de conexión a la base de datos
require_once '../Admin/DB.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header('Location: ../index.php'); // Redirige al inicio de sesión si no está logueado o no es administrador
    exit;
}

try {
    // Obtener conexión a la base de datos
    $db = getDB();
    
    // Obtener lista de proveedores
    $stmt = $db->prepare("SELECT id, nombre FROM proveedores ORDER BY nombre ASC");
    $stmt->execute();
    $proveedores = $stmt->get_result();

    // Procesar el formulario de agregar pedido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_proveedor = $_POST['id_proveedor'];
        $total = $_POST['total'];
        $estado = $_POST['estado'];

        $stmt = $db->prepare("INSERT INTO pedidos_proveedores (id_proveedor, fecha_pedido, estado, total) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("isd", $id_proveedor, $estado, $total);
        $stmt->execute();

        // Redirigir a pedidos.php después de agregar
        header("Location: pedidos.php");
        exit;
    }
} catch (Exception $e) {
    die("Error al procesar la solicitud: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/agregar_pedidos.css">
    <title>Agregar Pedido a Proveedor</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Agregar Pedido a Proveedor</h2>
            </div>
        </div>
    </header>

    <!-- Botón de volver -->
    <div class="back-button">
        <a href="pedidos.php"><i class="fas fa-arrow-left"></i> Volver a Pedidos</a>
    </div>

    <!-- Formulario de Agregar Pedido -->
    <div class="form-container">
        <h3>Agregar Nuevo Pedido</h3>
        <form method="POST" action="">
            <label for="id_proveedor">Proveedor:</label>
            <select name="id_proveedor" id="id_proveedor" required>
                <option value="">Seleccione un proveedor</option>
                <?php while ($row = $proveedores->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="total">Total del Pedido ($):</label>
            <input type="number" name="total" id="total" step="1" required>

            <label for="estado">Estado:</label>
            <select name="estado" id="estado" required>
                <option value="pendiente">Pendiente</option>
                <option value="en tránsito">En tránsito</option>
                <option value="recibido">Recibido</option>
                <option value="cancelado">Cancelado</option>
            </select>

            <button type="submit" class="btn-submit">Agregar Pedido</button>
        </form>
    </div>
</body>
</html>
