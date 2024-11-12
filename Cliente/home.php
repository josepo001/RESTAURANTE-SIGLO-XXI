<?php
$categoriaActual = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - Restaurante Siglo XXI</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                        url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        /* Card Styling */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.15);
        }

        /* Precio */
        .precio {
            color: #d97706; /* Tono cálido para el precio */
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Botón de agregar */
        .btn-agregar {
            background-color: #f59e0b;
            transition: background-color 0.3s ease;
        }

        .btn-agregar:hover {
            background-color: #d97706;
        }

        /* Categorías Activas */
        .categoria-activa {
            background-color: #d97706;
            color: white;
        }

        /* Carrito flotante */
        #carrito-flotante {
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Styling for Floating Cart */
        #carrito-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f59e0b;
            color: white;
            padding: 10px;
            border-radius: 50%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        /* Sombra suave */
        .shadow-light {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-stone-50">
    <!-- Barra de Navegación -->
    <nav class="bg-[#f59e0b] text-white py-4 px-6 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                </svg>
                <h1 class="text-2xl font-serif">Restaurante Siglo XXI</h1>
            </div>
            <div id="carrito-icon" class="relative cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span id="carrito-contador" class="absolute -top-2 -right-2 bg-white text-[#f59e0b] text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">0</span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div>
            <h2 class="text-4xl font-serif mb-4">Bienvenido a nuestra mesa</h2>
            <p class="text-xl">50 años de tradición y sabor casero</p>
        </div>
    </div>

    <!-- Categorías -->
    <div class="max-w-7xl mx-auto px-4 my-8">
        <div class="flex space-x-4 overflow-x-auto py-4">
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
                $activeClass = $categoriaActual === $cat['id'] ? 'categoria-activa' : 'bg-white hover:bg-[#f59e0b] hover:text-white';
                echo "<a href='?categoria={$cat['id']}' 
                        class='flex-shrink-0 px-6 py-2 rounded-full shadow-md transition-colors {$activeClass}'>
                        {$cat['nombre']}
                      </a>";
            }
            ?>
        </div>
    </div>

    <!-- Productos -->
<div class="max-w-7xl mx-auto px-4">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <div class="bg-white rounded-lg shadow-md overflow-hidden card-hover">
                    <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="w-full h-48 object-cover" onerror="this.src='img/default.jpg'">
                    <div class="p-6">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <div class="flex justify-between items-center">
                            <span class="precio">$<?php echo number_format($producto['precio'], 2); ?></span>
                            <button class="btn-agregar text-white px-4 py-2 rounded-full transition" onclick="agregarAlCarrito(<?php echo htmlspecialchars(json_encode($producto)); ?>)">
                                Agregar al pedido
                            </button>
                        </div>
                    </div>
                </div>
        <?php } } catch(PDOException $e) { echo "<p>Error: " . $e->getMessage() . "</p>"; } ?>
    </div>
</div>

    <!-- Carrito flotante -->
    <div id="carrito-flotante" class="fixed bottom-4 right-4 bg-white p-4 rounded-lg shadow-xl w-96 hidden">
        <h3 class="text-lg font-bold mb-4">Tu Pedido</h3>
        <div id="items-carrito"></div>
        <div class="border-t mt-4 pt-4">
            <div class="flex justify-between items-center mb-4">
                <span class="font-bold">Total:</span>
                <span id="total-carrito" class="text-xl font-bold">$0.00</span>
            </div>
            <form id="form-pedido" onsubmit="confirmarPedido(event)">
                <div class="mb-4">
                    <label for="nombre-cliente" class="block font-medium mb-2">Nombre del Cliente:</label>
                    <input type="text" id="nombre-cliente" name="nombre-cliente" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label for="mesa-select" class="block font-medium mb-2">Seleccionar Mesa:</label>
                    <select id="mesa-select" class="w-full border rounded-lg px-3 py-2" required>
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
                </div>
                <button type="submit" class="w-full bg-[#8B4513] text-white py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    Confirmar Pedido
                </button>
            </form>
        </div>
    </div>

    <!-- Referencias a scripts -->
    <script src="js/app.js"></script>
</body>
</html>
