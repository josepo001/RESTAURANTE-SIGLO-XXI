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
    <link rel="stylesheet" href="../css/homeAdmin.css">
    <title>Panel de Administrador</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI</h2>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li><a href="homeAdmin.php"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
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
        <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?></h1>
    </div>

    <!-- Contenido Principal -->
    <main style="margin: 20px;">
        <section class="activity-summary">
            <h2>Resumen de Actividad Reciente</h2>
            <table>
                <tr>
                    <th>Mesa</th>
                    <th>Cliente</th>
                    <th>Hora del Pedido</th>
                    <th>Estado</th>
                </tr>
                <?php
                // Consulta para obtener pedidos recientes
                $stmt = $db->prepare("SELECT mesa.numero AS mesa_numero, usuarios.nombre AS cliente, pedidos.fecha_pedido, pedidos.estado 
                                      FROM pedidos 
                                      JOIN mesas AS mesa ON pedidos.id_mesa = mesa.id 
                                      JOIN usuarios ON pedidos.id_usuario = usuarios.id 
                                      ORDER BY pedidos.fecha_pedido DESC LIMIT 5");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($pedido = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pedido['mesa_numero']) . "</td>";
                    echo "<td>" . htmlspecialchars($pedido['cliente']) . "</td>";
                    echo "<td>" . htmlspecialchars($pedido['fecha_pedido']) . "</td>";
                    echo "<td>" . htmlspecialchars($pedido['estado']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </section>

        <section class="low-stock">
            <h2>Productos con Stock Bajo</h2>
            <table>
                <tr>
                    <th>Producto</th>
                    <th>Stock</th>
                    <th>Acción</th>
                </tr>
                <?php
                // Consulta para productos con stock bajo
                $stmt = $db->prepare("SELECT nombre, stock FROM productos WHERE stock < 5 ORDER BY stock ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($producto = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($producto['stock']) . "</td>";
                    echo "<td><a href='pedidos.php'>Realizar Pedido</a></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </section>

        <section class="quick-access">
            <h2>Accesos Rápidos</h2>
            <div class="buttons">
                <a href="agregar_producto.php" class="btn">Agregar Producto</a>
                <a href="agregar_pedido.php" class="btn">Realizar Pedido a Proveedor</a>
                <a href="Registrar.php" class="btn">Registrar Nuevo Usuario</a>
            </div>
        </section>
    </main>
</body>
</html>
