<?php
// Iniciar sesión si aún no ha comenzado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivo de conexión
require_once '../Admin/DB.php';

// Verificar si el usuario ha iniciado sesión y tiene rol de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
    header('Location: ../index.php');
    exit;
}

// Verificar si se ha pasado un ID por la URL
if (!isset($_GET['id'])) {
    header('Location: productos.php?error=ID de producto no especificado');
    exit;
}

$id_producto = $_GET['id'];
try {
    // Obtener conexión a la base de datos
    $db = getDB();

    // Obtener datos del producto
    $stmt = $db->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();

    if (!$producto) {
        header('Location: productos.php?error=Producto no encontrado');
        exit;
    }

    // Procesar la actualización del producto
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];

        // Actualizar el producto
        $stmt = $db->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?");
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id_producto);

        if ($stmt->execute()) {
            header('Location: productos.php?success=Producto actualizado correctamente');
            exit;
        } else {
            $error = "Error al actualizar el producto: " . $stmt->error;
        }
    }

} catch (Exception $e) {
    die("Error al obtener información del producto: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/editar_productos.css">
</head>
<body>
    <h1>Editar Producto</h1>

    <!-- Mostrar mensaje de error si existe -->
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Formulario de edición de producto -->
    <form action="" method="post">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
        </div>
        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="1" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
        </div>
        <div class="form-group">
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
        </div>
        <button type="submit">Guardar Cambios</button>
        <a href="productos.php" class="btn-cancelar">Cancelar</a>
    </form>
</body>
</html>
