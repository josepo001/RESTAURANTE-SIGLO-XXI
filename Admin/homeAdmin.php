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
    <link rel="stylesheet" href="admin.css"> <!-- Enlace al CSS específico del panel de admin -->
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
    <!--Content -->
</body>
</html>
