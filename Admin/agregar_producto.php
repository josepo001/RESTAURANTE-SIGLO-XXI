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

// Inicializar variables para el formulario
$error = '';
$success = '';

// Procesar el formulario de envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    // Validar datos
    if (empty($nombre) || empty($precio) || empty($stock)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        try {
            // Conectar a la base de datos
            $db = getDB();

            // Insertar producto
            $stmt = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, fecha_agregado) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
            $stmt->execute();

            $success = 'Producto agregado exitosamente.';
        } catch (Exception $e) {
            $error = 'Error al agregar el producto: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/agregar_producto.css">
    <title>Agregar Producto</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Agregar Producto</h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="productos.php"><i class="fas fa-box"></i> Volver a Productos</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Mensajes de éxito o error -->
    <div class="messages">
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
    </div>

    <!-- Formulario para agregar producto -->
    <div class="form-container">
        <h1>Agregar Nuevo Producto</h1>
        <form method="POST" action="agregar_producto.php">
            <label for="nombre">Nombre del Producto*</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion"></textarea>

            <label for="precio">Precio*</label>
            <input type="number" id="precio" name="precio" step="1" required>

            <label for="stock">Stock*</label>
            <input type="number" id="stock" name="stock" required>

            <button type="submit" class="button">Agregar Producto</button>
        </form>
    </div>
</body>
</html>
