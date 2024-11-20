<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'Admin/DB.php'; // Asegúrate de que esta ruta es correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); // Capturar el email y eliminar espacios
    $password = $_POST['password'] ?? ''; // Capturar la contraseña

    try {
        // Conexión a la base de datos
        $db = getDB();

        // Consulta para obtener el usuario por email
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $db->error);
        }

        $stmt->bind_param("s", $email); // Vincular el email como parámetro
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si el usuario existe
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Obtener los datos del usuario

            // Verificar la contraseña usando password_verify
            if (password_verify($password, $user['contraseña'])) {
                // Guardar información en la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_name'] = $user['nombre'];

                // Redirigir al usuario según su rol
                switch ($user['rol']) {
                    case 'administrador':
                        header('Location: Admin/homeAdmin.php');
                        break;
                    case 'bodega':
                        header('Location: Bodega/homeb.php');
                        break;
                    case 'finanzas':
                        header('Location: Finanzas/homef.php');
                        break;
                    case 'cocina':
                        header('Location: Cocina/homec.php');
                        break;
                    default:
                        $_SESSION['error'] = "Rol de usuario no reconocido.";
                        header('Location: index.php');
                        exit;
                }
                exit;
            } else {
                // Contraseña incorrecta
                $_SESSION['error'] = "Contraseña incorrecta.";
                header('Location: index.php');
                exit;
            }
        } else {
            // Usuario no encontrado
            $_SESSION['error'] = "Correo no registrado.";
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        // Manejo de errores
        $_SESSION['error'] = "Error en el sistema: " . $e->getMessage();
        header('Location: error.php');
        exit;
    }
} else {
    // Si no es un método POST, redirigir al formulario de inicio de sesión
    header('Location: index.php');
    exit;
}
?>
