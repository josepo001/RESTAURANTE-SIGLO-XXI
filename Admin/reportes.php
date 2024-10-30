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

    // Ingresos y Egresos Totales
    $stmt = $db->prepare("
        SELECT 
            SUM(CASE WHEN tipo_transaccion = 'ingreso_venta' THEN ingresos ELSE 0 END) AS total_ingresos,
            SUM(CASE WHEN tipo_transaccion = 'egreso_compra' THEN egresos ELSE 0 END) AS total_egresos
        FROM finanzas
    ");
    $stmt->execute();
    $finanzas = $stmt->get_result()->fetch_assoc();
    $utilidad_neta = $finanzas['total_ingresos'] - $finanzas['total_egresos'];

    // Pedidos a Proveedores (últimos tres)
    $stmt = $db->prepare("
        SELECT proveedores.nombre AS proveedor, pedidos_proveedores.fecha_pedido, pedidos_proveedores.estado, pedidos_proveedores.total
        FROM pedidos_proveedores
        JOIN proveedores ON pedidos_proveedores.id_proveedor = proveedores.id
        ORDER BY pedidos_proveedores.fecha_pedido DESC
        LIMIT 3
    ");
    $stmt->execute();
    $pedidos_proveedores = $stmt->get_result();

    // Ventas por Producto
    $stmt = $db->prepare("
        SELECT productos.nombre, SUM(detalle_pedidos.cantidad) AS cantidad_vendida, SUM(detalle_pedidos.cantidad * detalle_pedidos.precio_unitario) AS total_venta
        FROM detalle_pedidos
        JOIN productos ON detalle_pedidos.id_producto = productos.id
        GROUP BY productos.nombre
        ORDER BY total_venta DESC
    ");
    $stmt->execute();
    $ventas_productos = $stmt->get_result();

    // Reporte de Inventario
    $stmt = $db->prepare("
        SELECT nombre, stock, stock_minimo
        FROM ingredientes
        ORDER BY stock ASC
    ");
    $stmt->execute();
    $inventario = $stmt->get_result();

} catch (Exception $e) {
    die("Error al obtener datos de reportes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/reportes.css">
    <title>Panel de Administración - Reportes</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Reportes</h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="homeAdmin.php"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li><a href="productos.php"><i class="fas fa-box"></i> Productos</a></li>
                    <li><a href="mesas.php"><i class="fas fa-chair"></i> Mesas</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-truck"></i> Pedidos</a></li>
                    <li><a href="perfilAdmin.php"><i class="fas fa-user"></i> Mi Perfil</a></li>
                    <li><a href="../cerrar-sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenedor de Reportes -->
    <div class="reportes-container">
        <!-- Ingresos y Egresos Totales -->
        <div class="reporte-seccion">
            <h3>Ingresos y Egresos Totales</h3>
            <p><strong>Ingresos:</strong> $<?php echo number_format($finanzas['total_ingresos'], 2); ?></p>
            <p><strong>Egresos:</strong> $<?php echo number_format($finanzas['total_egresos'], 2); ?></p>
            <p><strong>Utilidad Neta:</strong> $<?php echo number_format($utilidad_neta, 2); ?></p>
            <a href="reporte_ingresos_egresos.php" class="btn-descargar">Descargar PDF</a>
        </div>

        <!-- Pedidos a Proveedores (últimos tres) -->
        <div class="reporte-seccion">
            <h3>Pedidos a Proveedores</h3>
            <table>
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $pedidos_proveedores->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['proveedor']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                            <td>$<?php echo number_format($pedido['total'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="reporte_pedidos_proveedores.php" class="btn-descargar">Descargar PDF</a>
        </div>

        <!-- Ventas por Producto -->
        <div class="reporte-seccion">
            <h3>Ventas por Producto</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad Vendida</th>
                        <th>Total en Ventas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($venta = $ventas_productos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($venta['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($venta['cantidad_vendida']); ?></td>
                            <td>$<?php echo number_format($venta['total_venta'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="reporte_ventas_productos.php" class="btn-descargar">Descargar PDF</a>
        </div>

        <!-- Reporte de Inventario -->
        <div class="reporte-seccion">
            <h3>Reporte de Inventario</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ingrediente</th>
                        <th>Stock</th>
                        <th>Stock Mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $inventario->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($item['stock']); ?></td>
                            <td><?php echo htmlspecialchars($item['stock_minimo']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="reporte_inventario.php" class="btn-descargar">Descargar PDF</a>
        </div>
    </div>
</body>
</html>
