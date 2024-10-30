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
    if (!$db) {
        die("Error de conexión a la base de datos.");
    }

    // Obtener información del usuario logueado
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Filtrar productos por búsqueda
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT * FROM productos";
    if (!empty($search)) {
        $query .= " WHERE nombre LIKE ?";
    }
    $stmt = $db->prepare($query);

    // Si hay una búsqueda, añadir el parámetro al statement
    if (!empty($search)) {
        $searchParam = '%' . $search . '%';
        $stmt->bind_param("s", $searchParam);
    }

    $stmt->execute();
    $productos = $stmt->get_result();

    // Procesar formulario para eliminar productos
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: productos.php");
        exit;
    }

} catch (Exception $e) {
    die("Error al obtener información del usuario: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/productos.css">
    <title>Panel de Administración - Productos</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Gestión de Productos</h2>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li><a href="homeAdmin.php"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li><a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a></li>
                    <li><a href="productos.php"><i class="fas fa-box"></i> Productos</a></li>
                    <li><a href="mesas.php"><i class="fas fa-chair"></i> Mesas</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-truck"></i> Pedidos</a></li>
                    <li><a href="reportes.php"><i class="fas fa-chart-line"></i> Reportes</a></li>
                    <li><a href="perfilAdmin.php"><i class="fas fa-user"></i> Mi Perfil</a></li>
                    <li><a href="../cerrar-sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>

            <div class="user-info">
                <i class="fas fa-user-circle" style="font-size: 24px;"></i>
                <span><?php echo htmlspecialchars($user['nombre'] . ' ' . ($user['apellido'] ?? '')); ?></span>
                <small><?php echo ucfirst($user['rol']); ?></small>
            </div>
        </div>
    </header>

    <div style="text-align: left; margin-top: 20px;">
        <h1>Gestión de Productos</h1>
    </div>

    <!-- Formulario de Búsqueda -->
    <div class="search-container">
        <form method="GET" action="" id="searchForm">
            <input type="text" name="search" id="searchInput" placeholder="Buscar" value="<?php echo htmlspecialchars($search); ?>">
            <button id="searchButton">Buscar</button>
            <button type="button" id="clearButton">Restablecer Búsqueda</button>
        </form>
    </div>

    <!-- Botón de agregar producto -->
    <div class="add-product-btn">
        <a href="agregar_producto.php" class="button">Agregar Producto</a>
    </div>

    <!-- Tabla de productos -->
    <div class="productos-container">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Fecha Agregado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $productos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($row['precio']); ?></td>
                        <td><?php echo htmlspecialchars($row['stock']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_agregado']); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="accion" value="eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Script para restablecer la búsqueda
        document.getElementById('clearButton').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('searchInput').value = '';
            document.getElementById('searchForm').submit();
        });
    </script>
</body>
</html>
