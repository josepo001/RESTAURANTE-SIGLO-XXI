<?php
// Iniciar la sesión si aún no ha comenzado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir el archivo de conexión a la base de datos
require_once '../Admin/DB.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header('Location: ../index.php');
    exit;
}

// Obtener el ID del pedido a proveedores a editar
if (!isset($_GET['id'])) {
    header("Location: pedidos.php");
    exit;
}
$pedido_id = $_GET['id'];

try {
    // Obtener conexión a la base de datos
    $db = getDB();

    // Si el formulario ha sido enviado, actualizar el pedido a proveedor
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevo_estado = $_POST['estado'];
        $total = $_POST['total'];

        // Actualizar los datos del pedido a proveedor
        $stmt = $db->prepare("UPDATE pedidos_proveedores SET estado = ?, total = ? WHERE id = ?");
        $stmt->bind_param("sdi", $nuevo_estado, $total, $pedido_id);
        $stmt->execute();

        // Redirigir a la lista de pedidos después de actualizar
        header("Location: pedidos.php");
        exit;
    }

    // Obtener los detalles del pedido actual
    $stmt = $db->prepare("
        SELECT id, estado, total, id_proveedor, fecha_pedido
        FROM pedidos_proveedores
        WHERE id = ?
    ");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $pedido = $stmt->get_result()->fetch_assoc();

    if (!$pedido) {
        echo "Pedido no encontrado";
        exit;
    }
    
} catch (Exception $e) {
    die("Error al obtener información del pedido: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/editar_pedido.css">
    <title>Editar Pedido a Proveedor</title>
</head>
<body>
    <div class="container">
        <h2>Editar Pedido a Proveedor</h2>
        <form action="editar_pedido.php?id=<?php echo $pedido_id; ?>" method="POST">
            <label for="estado">Estado del Pedido:</label>
            <select id="estado" name="estado">
                <option value="pendiente" <?php if($pedido['estado'] === 'pendiente') echo 'selected'; ?>>Pendiente</option>
                <option value="en tránsito" <?php if($pedido['estado'] === 'en tránsito') echo 'selected'; ?>>En Tránsito</option>
                <option value="recibido" <?php if($pedido['estado'] === 'recibido') echo 'selected'; ?>>Recibido</option>
                <option value="cancelado" <?php if($pedido['estado'] === 'cancelado') echo 'selected'; ?>>Cancelado</option>
            </select>

            <label for="total">Total:</label>
            <input type="number" id="total" name="total" step="1" value="<?php echo htmlspecialchars($pedido['total']); ?>" required>
            
            <label for="fecha_pedido">Fecha del Pedido:</label>
            <input type="datetime-local" id="fecha_pedido" name="fecha_pedido" value="<?php echo date('Y-m-d\TH:i', strtotime($pedido['fecha_pedido'])); ?>" readonly>

            <button type="submit" class="btn-guardar">Guardar Cambios</button>
            <a href="pedidos.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>
