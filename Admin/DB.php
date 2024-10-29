<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'restaurante_siglo_xxi');

class Database {
    private $connection;

    // Constructor que inicia la conexión automáticamente
    public function __construct() {
        $this->connect();
    }

    // Método para conectar a la base de datos
    private function connect() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Verificar si hay un error en la conexión
        if ($this->connection->connect_error) {
            die("Error de conexión: " . $this->connection->connect_error);
        }
        
        // Establecer el conjunto de caracteres a UTF-8
        $this->connection->set_charset("utf8");
    }

    // Método para obtener la conexión actual
    public function getConnection() {
        return $this->connection;
    }

    // Método para ejecutar consultas SQL con parámetros
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $this->connection->error);
        }

        // Si hay parámetros, los asociamos a la consulta
        if ($params) {
            // Determinar los tipos de datos de los parámetros
            $types = '';
            foreach ($params as $param) {
                $types .= is_int($param) ? 'i' : (is_double($param) ? 'd' : 's');
            }
            $stmt->bind_param($types, ...$params);
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Verificar si la consulta es de tipo SELECT
        if (stripos($sql, 'SELECT') === 0) {
            $result = $stmt->get_result();
            return $result;
        }

        // Para consultas de tipo INSERT, UPDATE o DELETE, devolvemos el número de filas afectadas
        return $stmt->affected_rows;
    }

    // Método para cerrar la conexión
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Función para obtener la instancia de la base de datos (singleton)
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db->getConnection();
}
?>
