<?php
$categoriaActual = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - Restaurante Siglo XXI</title>
    
    <!-- Vincular el archivo CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="header">
        <div class="header-container">
            <div class="logo">
                
                <h1>Restaurante Siglo XXI</h1>
            </div>
            <div id="carrito-icon" class="carrito-icon">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
    </svg>
    <span id="carrito-contador" class="contador">0</span>
</div>

        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <h2>Bienvenido a nuestra mesa</h2>
        <p>50 años de tradición y sabor casero</p>
    </div>

    <!-- Categorías -->
    <div class="categorias">
        <?php
        $categorias = [
            ['id' => 'todos', 'nombre' => 'Todos'],
            ['id' => 'entradas', 'nombre' => 'Entradas'],
            ['id' => 'principales', 'nombre' => 'Platos Principales'],
            ['id' => 'ensaladas', 'nombre' => 'Ensaladas'],
            ['id' => 'bebidas', 'nombre' => 'Bebidas'],
            ['id' => 'postres', 'nombre' => 'Postres']
        ];

        foreach ($categorias as $cat) {
            $activeClass = $categoriaActual === $cat['id'] ? 'categoria-activa' : '';
            echo "<a href='?categoria={$cat['id']}' class='categoria-link $activeClass'>{$cat['nombre']}</a>";
        }
        ?>
    </div>

    <!-- Productos -->
    <div id="productos-container" class="productos-container <?php echo 'categoria-' . $categoriaActual; ?>">
        <?php
        try {
            require_once 'config/database.php';

            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "
                SELECT p.*, 
                       CASE WHEN r.id_producto IS NULL THEN 1 
                            WHEN MIN(i.stock >= r.cantidad) THEN 1 
                            ELSE 0 END as disponible
                FROM productos p
                LEFT JOIN recetas r ON p.id = r.id_producto
                LEFT JOIN ingredientes i ON r.id_ingrediente = i.id
                WHERE 1=1
            ";

            if ($categoriaActual !== 'todos') {
                $query .= " AND p.categoria = :categoria";
            }

            $query .= " GROUP BY p.id";

            $stmt = $conn->prepare($query);
            if ($categoriaActual !== 'todos') {
                $stmt->bindParam(':categoria', $categoriaActual);
            }
            $stmt->execute();

            while($producto = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $imagen = $producto['imagen'] ? $producto['imagen'] : "img/default.jpg";
                $disponible = $producto['disponible'] == 1;
        ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" onerror="this.src='img/default.jpg'">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <div class="product-footer">
                            <span class="precio">$<?php echo number_format($producto['precio'], 2); ?></span>
                            <?php if ($disponible): ?>
                                <button class="btn-agregar" onclick="agregarAlCarrito(<?php echo htmlspecialchars(json_encode($producto)); ?>)">Agregar</button>
                            <?php else: ?>
                                <button class="btn-no-disponible" disabled>No disponible</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php } } catch(PDOException $e) { echo "<p>Error: " . $e->getMessage() . "</p>"; } ?>
    </div>

    <!-- Carrito flotante -->
    <div id="carrito-flotante" class="carrito-flotante hidden">
        <h3>Tu Pedido</h3>
        <div id="items-carrito"></div>
        <div class="carrito-total">
            <span>Total:</span>
            <span id="total-carrito">$0.00</span>
        </div>
        <form id="form-pedido" onsubmit="confirmarPedido(event)">
            <input type="text" id="nombre-cliente" name="nombre-cliente" placeholder="Nombre del Cliente" required>
            <select id="mesa-select" required>
                <option value="">Seleccione una mesa</option>
                <?php
                try {
                    $stmt = $conn->query("SELECT * FROM mesas WHERE estado = 'disponible'");
                    while($mesa = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$mesa['id']}'>Mesa {$mesa['numero']} ({$mesa['capacidad']} personas)</option>";
                    }
                } catch(PDOException $e) {
                    echo "<option value=''>Error al cargar mesas</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-confirmar">Confirmar Pedido</button>
        </form>
    </div>

    <!-- Scripts -->
    <script src="js/app.js"></script>
</body>
</html>
