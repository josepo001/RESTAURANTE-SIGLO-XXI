<?php
session_start();
require_once '../Admin/DB.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

try {
    $db = getDB(); // Obtener conexión a la base de datos
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']); // Vincular el parámetro user_id
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Obtener el resultado como un array asociativo

    if (!$user) {
        // Si no se encuentra el usuario, redirige o muestra un mensaje
        $_SESSION['mensaje'] = "Usuario no encontrado.";
        header('Location: ../index.php');
        exit;
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/perfiladmin.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI </h2>
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
                <i class="fas fa-user-circle" style="font-size: 24px; margin-right: 8px;"></i>
                <span>
                    <?php echo htmlspecialchars($user['nombre'] ?? ''); ?>
                    
                </span>
                <br>
                <small><?php echo htmlspecialchars(ucfirst($user['rol'] ?? '')); ?></small>
            </div>
        </div>
    </header>

    <div class="main-content">
        <h1>Mi Perfil</h1>
        <form action="procesar_editar.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre'] ?? ''); ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            <br>
            <label for="password">Nueva Contraseña (opcional):</label>
            <input type="password" id="password" name="password">
            <br>
            <button type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>
