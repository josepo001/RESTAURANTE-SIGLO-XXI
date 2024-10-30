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

    // Procesar el formulario de agregar proveedor
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $contacto = $_POST['contacto'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];

        $stmt = $db->prepare("INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $contacto, $telefono, $email, $direccion);
        $stmt->execute();

        // Redirigir a la lista de proveedores después de agregar
        header("Location: proveedores.php");
        exit;
    }
} catch (Exception $e) {
    die("Error al procesar la solicitud: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/agregar_proveedor.css">
    <title>Agregar Proveedor</title>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h2>Restaurante Siglo XXI - Agregar Proveedor</h2>
            </div>
        </div>
    </header>

    <!-- Botón de volver -->
    <div class="back-button">
        <a href="pedidos.php"><i class="fas fa-arrow-left"></i> Volver a Proveedores</a>
    </div>

    <!-- Formulario de Agregar Proveedor -->
    <div class="form-container">
        <h3>Agregar Nuevo Proveedor</h3>
        <form method="POST" action="">
            <label for="nombre">Nombre del Proveedor:</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="contacto">Contacto:</label>
            <input type="text" name="contacto" id="contacto" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="direccion">Dirección:</label>
            <textarea name="direccion" id="direccion" rows="4" required></textarea>

            <button type="submit" class="btn-submit">Agregar Proveedor</button>
        </form>
    </div>
</body>
</html>
