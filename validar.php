<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'Admin/DB.php'; // Asegúrate de que esta función conecta correctamente a la base de datos

// Si la solicitud es POST, intentamos iniciar sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? ''); // Capturamos el email y eliminamos espacios
    $password = $_POST['password'] ?? ''; // Capturamos la contraseña

    try {
        // Obtener la conexión a la base de datos
        $db = getDB();
        
        // Preparamos una consulta para encontrar al usuario según el email
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email); // Vinculamos el parámetro
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc(); // Obtenemos el resultado como un array asociativo

        // Si el usuario existe y la contraseña es correcta (sin encriptación por ahora)
        if ($user && $password === $user['contraseña']) { // Cambiar a password_verify cuando las contraseñas estén encriptadas
            // Guardar la información relevante en la sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['user_name'] = $user['nombre'];

            // Redirigir al usuario según su rol
            switch ($user['rol']) {
                case 'administrador':
                    header('Location: Admin/homeAdmin.php');
                    break;
                case 'bodega':
                    header('Location: Bodega/homeBodega.php');
                    break;
                case 'finanzas':
                    header('Location: Finanzas/homeFinanzas.php');
                    break;
                case 'cocina':
                    header('Location: Cocina/homeCocina.php');
                    break;
                default:
                    $_SESSION['error'] = "Rol de usuario no reconocido.";
                    header('Location: login.php');
                    exit;
            }
            exit;
        } else {
            // Si las credenciales son inválidas
            $_SESSION['error'] = "Correo o contraseña inválidos.";
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        // Manejo de errores de la base de datos
        $_SESSION['error'] = "Error en el sistema: " . $e->getMessage();
        header('Location: error.php');
        exit;
    }
} else {
    // Si la solicitud no es POST, redirigimos al formulario de inicio de sesión
    header('Location: index.php');
    exit;
}
?>
