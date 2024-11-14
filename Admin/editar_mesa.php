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

    // Verificar si se ha pasado un ID por la URL
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Obtener información de la mesa a editar
        $stmt = $db->prepare("SELECT * FROM mesas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $mesa = $resultado->fetch_assoc(); // Cargar los datos de la mesa a editar
        } else {
            echo "Mesa no encontrada.";
            exit; // Detener ejecución si la mesa no existe
        }
    } else {
        echo "ID de mesa no especificado.";
        exit; // Detener ejecución si no se especifica el ID
    }
} catch (Exception $e) {
    die("Error al obtener información de la mesa: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mesa</title>
    <link rel="stylesheet" href="../css/editar_mesas.css">
</head>
<body>
    <div style="text-align: left;">
        <h1>Editar Mesa</h1>
    </div>

    <!-- FORMULARIO -->
    <form class="profile-form" action="procesar_editar_mesa.php" method="post">
        <input type="hidden" value="<?php echo htmlspecialchars($mesa['id'] ?? ''); ?>" name="id">        

        <div class="form-group">
            <label for="inputNumero"><b>Número de Mesa</b></label>
            <input id="inputNumero" class="form-control" type="text" value="<?php echo htmlspecialchars($mesa['numero'] ?? ''); ?>" name="numero" required>
        </div>

        <div class="form-group">
            <label for="inputCapacidad"><b>Capacidad</b></label>
            <input id="inputCapacidad" class="form-control" type="number" value="<?php echo htmlspecialchars($mesa['capacidad'] ?? ''); ?>" name="capacidad" required>
        </div>

        <div class="form-group">
            <label for="estadoMesa"><b>Estado</b></label>
            <select name="estado" id="estadoMesa">
                <option value="disponible" <?php echo ($mesa['estado'] === 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                <option value="ocupada" <?php echo ($mesa['estado'] === 'ocupada') ? 'selected' : ''; ?>>Ocupada</option>
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn-actualizar">Actualizar</button>
            <a href="mesas.php" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
    <!-- FIN FORMULARIO -->

</body>
</html>
