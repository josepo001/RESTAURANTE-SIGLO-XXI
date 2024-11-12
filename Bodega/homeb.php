<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Bodega - Restaurante Siglo XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styling for a warm, rustic look */
        .bg-wood {
            background-color: #D7BFA5; /* Light wood color */
        }
        .text-wood {
            color: #8B4513; /* Dark wood color */
        }
        .bg-card {
            background-color: #F5ECE2; /* Light beige for cards */
        }
    </style>
</head>
<body class="bg-wood">
    <!-- Barra de Navegación -->
    <nav class="bg-[#8B4513] text-white py-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6">
            <h1 class="text-3xl font-serif font-bold">Sistema Bodega</h1>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-gray-300 text-lg" onclick="mostrarSeccion('productos')">Productos</a>
                <a href="#" class="hover:text-gray-300 text-lg" onclick="mostrarSeccion('recetas')">Recetas</a>
                <a href="#" class="hover:text-gray-300 text-lg" onclick="mostrarSeccion('pedidos')">Pedidos Proveedores</a>
                <a href="../cerrar-sesion.php" class="hover:text-gray-300 text-lg">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-12">
        <!-- Resumen General -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-card p-8 rounded-lg shadow-lg text-center border border-[#8B4513]">
                <h3 class="text-lg font-semibold text-wood mb-2">Productos en Stock</h3>
                <p class="text-3xl font-bold text-blue-600" id="total-productos">0</p>
            </div>
            <div class="bg-card p-8 rounded-lg shadow-lg text-center border border-[#8B4513]">
                <h3 class="text-lg font-semibold text-wood mb-2">Productos Bajos en Stock</h3>
                <p class="text-3xl font-bold text-red-600" id="productos-bajos">0</p>
            </div>
            <div class="bg-card p-8 rounded-lg shadow-lg text-center border border-[#8B4513]">
                <h3 class="text-lg font-semibold text-wood mb-2">Pedidos Pendientes</h3>
                <p class="text-3xl font-bold text-yellow-600" id="pedidos-pendientes">0</p>
            </div>
        </div>

        <!-- Sección de Productos -->
        <div id="seccion-productos" class="bg-card p-8 rounded-lg shadow-lg mb-12 border border-[#8B4513]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-wood">Gestión de Productos</h2>
                <button onclick="mostrarFormularioProducto()" 
                        class="bg-[#8B4513] text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    Agregar Producto
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-300 rounded-lg">
                    <thead class="bg-[#f8f2ec]">
                        <tr>
                            <th class="px-6 py-3 text-wood font-semibold">Nombre</th>
                            <th class="px-6 py-3 text-wood font-semibold">Stock Actual</th>
                            <th class="px-6 py-3 text-wood font-semibold">Stock Mínimo</th>
                            <th class="px-6 py-3 text-wood font-semibold">Unidad</th>
                            <th class="px-6 py-3 text-wood font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-productos">
                        <!-- Productos cargados dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección de Recetas -->
        <div id="seccion-recetas" class="bg-card p-8 rounded-lg shadow-lg mb-12 border border-[#8B4513] hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-wood">Gestión de Recetas</h2>
                <button onclick="mostrarFormularioReceta()" 
                        class="bg-[#8B4513] text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    Nueva Receta
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-300 rounded-lg">
                    <thead class="bg-[#f8f2ec]">
                        <tr>
                            <th class="px-6 py-3 text-wood font-semibold">Producto</th>
                            <th class="px-6 py-3 text-wood font-semibold">Ingredientes Necesarios</th>
                            <th class="px-6 py-3 text-wood font-semibold">Estado Stock</th>
                            <th class="px-6 py-3 text-wood font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-recetas">
                        <!-- Recetas cargadas dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección de Pedidos -->
        <div id="seccion-pedidos" class="bg-card p-8 rounded-lg shadow-lg mb-12 border border-[#8B4513] hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-wood">Pedidos a Proveedores</h2>
                <button onclick="mostrarFormularioPedido()" 
                        class="bg-[#8B4513] text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    Nuevo Pedido
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-300 rounded-lg">
                    <thead class="bg-[#f8f2ec]">
                        <tr>
                            <th class="px-6 py-3 text-wood font-semibold">Proveedor</th>
                            <th class="px-6 py-3 text-wood font-semibold">Fecha</th>
                            <th class="px-6 py-3 text-wood font-semibold">Estado</th>
                            <th class="px-6 py-3 text-wood font-semibold">Total</th>
                            <th class="px-6 py-3 text-wood font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-pedidos">
                        <!-- Pedidos cargados dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/bodega.js"></script>
</body>
</html>
