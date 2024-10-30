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
    
    // Procesar cambio de estado de los pedidos a proveedores
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_estado_proveedor') {
        $id = $_POST['id'];
        $nuevo_estado = $_POST['estado'];
        $stmt = $db->prepare("UPDATE pedidos_proveedores SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevo_estado, $id);
        $stmt->execute();
        header("Location: pedidos.php");
        exit;
    }

    // Obtener lista de pedidos a proveedores
    $stmt = $db->prepare("
        SELECT pedidos_proveedores.id, pedidos_proveedores.fecha_pedido, pedidos_proveedores.estado,
               pedidos_proveedores.total, proveedores.nombre AS proveedor_nombre
        FROM pedidos_proveedores
        JOIN proveedores ON pedidos_proveedores.id_proveedor = proveedores.id
        ORDER BY pedidos_proveedores.fecha_pedido ASC
    ");
    $stmt->execute();
    $pedidos_proveedores = $stmt->get_result();

} catch (Exception $e) {
    die("Error al obtener información de los pedidos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/pedidos.css">
    <title>Panel de Administración - Pedidos a Proveedores</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Gestión de Pedidos a Proveedores</h2>
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
        </div>
    </header>

    <!-- Título de la página y botón de agregar pedido -->
    <div class="section-header">
        <h2>Pedidos a Proveedores</h2>
        <a href="agregar_proveedor.php" class="btn-agregar">Agregar Proveedor</a>
        <a href="agregar_pedido.php" class="btn-agregar">Agregar Pedido</a>
    </div>

    <!-- Tabla de pedidos a proveedores -->
    <div class="pedidos-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pedidos_proveedores->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['proveedor_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_pedido']); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="accion" value="cambiar_estado_proveedor">
                                <select name="estado" onchange="this.form.submit()">
                                    <option value="pendiente" <?php if ($row['estado'] === 'pendiente') echo 'selected'; ?>>Pendiente</option>
                                    <option value="en tránsito" <?php if ($row['estado'] === 'en tránsito') echo 'selected'; ?>>En tránsito</option>
                                    <option value="recibido" <?php if ($row['estado'] === 'recibido') echo 'selected'; ?>>Recibido</option>
                                    <option value="cancelado" <?php if ($row['estado'] === 'cancelado') echo 'selected'; ?>>Cancelado</option>
                                </select>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars(number_format($row['total'], 2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
