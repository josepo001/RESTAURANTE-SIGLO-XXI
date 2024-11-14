<?php
// Iniciar sesión si aún no ha comenzado
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

try {
    // Obtener conexión a la base de datos
    $db = getDB();

    // Procesar la eliminación del proveedor
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_proveedor'])) {
        $id_proveedor = $_POST['id_proveedor'];
        
        $stmt = $db->prepare("DELETE FROM proveedores WHERE id = ?");
        $stmt->bind_param("i", $id_proveedor);
        
        if ($stmt->execute()) {
            header("Location: pedidos.php?success=Proveedor eliminado correctamente");
            exit;
        } else {
            $error = "Error al eliminar el proveedor.";
        }
    }

    // Obtener la lista de proveedores para el desplegable
    $stmt = $db->prepare("SELECT id, nombre FROM proveedores");
    $stmt->execute();
    $proveedores = $stmt->get_result();

} catch (Exception $e) {
    die("Error al obtener información de los proveedores: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Proveedor</title>
    <link rel="stylesheet" href="../css/eliminar_proveedor.css">
</head>
<body>
    <div class="container">
        <h2>Eliminar Proveedor</h2>
        
        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="id_proveedor">Selecciona un Proveedor:</label>
            <select name="id_proveedor" id="id_proveedor" required>
                <option value="">-- Seleccionar --</option>
                <?php while ($row = $proveedores->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="btn-eliminar">Eliminar Proveedor</button>
            <a href="pedidos.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>
