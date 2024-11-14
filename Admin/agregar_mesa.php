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
    $numero = $_POST['numero'];
    $capacidad = $_POST['capacidad'];
    $estado = $_POST['estado'];

    // Validar datos
    if (empty($numero) || empty($capacidad) || empty($estado)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        try {
            // Conectar a la base de datos
            $db = getDB();

            // Insertar mesa
            $stmt = $db->prepare("INSERT INTO mesas (numero, capacidad, estado) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $numero, $capacidad, $estado);
            if ($stmt->execute()) {
                $success = 'Mesa agregada exitosamente.';
            } else {
                $error = 'Error al agregar la mesa.';
            }
        } catch (Exception $e) {
            $error = 'Error al agregar la mesa: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/agregar_mesa.css">
    <title>Agregar Mesa</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Agregar Mesa</h2>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="mesas.php"><i class="fas fa-chair"></i> Volver a Mesas</a></li>
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

    <!-- Formulario para agregar mesa -->
    <div class="form-container">
        <h1>Agregar Nueva Mesa</h1>
        <form method="POST" action="agregar_mesa.php">
            <label for="numero">Número de Mesa*</label>
            <input type="number" id="numero" name="numero" required min="1" placeholder="Ej. 1">

            <label for="capacidad">Capacidad*</label>
            <input type="number" id="capacidad" name="capacidad" required min="1" placeholder="Ej. 4">

            <label for="estado">Estado*</label>
            <select id="estado" name="estado" required>
                <option value="disponible">Disponible</option>
                <option value="ocupada">Ocupada</option>
            </select>

            <button type="submit" class="button">Agregar Mesa</button>
        </form>
    </div>
</body>
</html>
