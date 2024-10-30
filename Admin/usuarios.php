<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../Admin/DB.php'; // Asegúrate de que la ruta sea correcta

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

try {
    // Conexión a la base de datos
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

// Captura del término de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Consulta para listar usuarios con búsqueda
    $sql = "SELECT * FROM usuarios WHERE 
            (nombre LIKE ? OR 
            email LIKE ? OR 
            id LIKE ?) 
            ORDER BY nombre ASC"; // Ordenado por nombre

    $stmt = $db->prepare($sql);
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Estadísticas de usuarios por rol
    $stats = [];
    $roles = ['administrador', 'cliente', 'bodega', 'finanzas', 'cocina'];
    foreach ($roles as $rol) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = ?");
        $stmt->bind_param("s", $rol);
        $stmt->execute();
        $stats['total_' . $rol] = $stmt->get_result()->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    die("Error al obtener la lista de usuarios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Usuarios</title>
    <link rel="stylesheet" href="../css/usuarios.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        function confirmarEliminar(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                document.getElementById('eliminarForm' + id).submit();
            }
        }
    </script>
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
                <span><?php echo htmlspecialchars($user['nombre']); ?></span>
                <small><?php echo ucfirst($user['rol']); ?></small>
            </div>
        </div>
    </header>

    <div style="text-align: left;">
        <h1>Gestión de Usuarios</h1>
    </div>

    <!-- Formulario de Búsqueda -->
    <div class="search-container">
        <form method="GET" action="" id="searchForm">
            <input type="text" name="search" id="searchInput" placeholder="Buscar" value="<?php echo htmlspecialchars($search); ?>">
            <button id="searchButton">Buscar</button>
            <button type="button" id="clearButton">Restablecer Búsqueda</button>
        </form>
    </div>

    <script>
        document.getElementById('clearButton').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('searchInput').value = '';
            document.getElementById('searchForm').submit();
        });
    </script>

    <!-- Contenido -->
    <div class="home_content">
        <br>
        <div class="container table-responsive">
            <table class="table table-light table-bordered border-secondary table-rounded">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Email</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Fecha Registro</th>
                        <th scope="col">Editar</th>
                        <th scope="col">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($usuario = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['fecha_registro']); ?></td>
                                <td>
                                    <a class="btn btn-success btn-sm" href="editar.php?id=<?php echo htmlspecialchars($usuario['id']); ?>">Editar</a>
                                </td>
                                <td>
                                    <form id="eliminarForm<?php echo $usuario['id']; ?>" action="eliminar.php" method="post">
                                        <input type="hidden" value="<?php echo htmlspecialchars($usuario['id']); ?>" name="txtID">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?php echo htmlspecialchars($usuario['id']); ?>)">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No se encontraron usuarios.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="button-container">
            <a class="btn_agregar" href="Registrar.php">AGREGAR USUARIO</a>
        </div>
    </div>
</body>
</html>
