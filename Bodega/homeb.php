<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Bodega - Restaurante Siglo XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Agregamos estilos personalizados para los inputs -->
    <style>
        input[type="text"], input[type="number"] {
            border: 1px solid #e5e7eb;
            outline: none;
            padding: 0.5rem;
        }
        input[type="text"]:focus, input[type="number"]:focus {
            border-color: #8B4513;
            ring: 2px;
            ring-color: #8B4513;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-[#8B4513] text-white py-4 px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-serif">Sistema Bodega</h1>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-gray-300" onclick="mostrarSeccion('productos')">Productos</a>
                <a href="#" class="hover:text-gray-300" onclick="mostrarSeccion('recetas')">Recetas</a>
                <a href="#" class="hover:text-gray-300" onclick="mostrarSeccion('pedidos')">Pedidos Proveedores</a>
                <a href="../cerrar-sesion.php" class="hover:text-gray-300">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Resumen General -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold mb-2">Productos en Stock</h3>
                <p class="text-2xl text-blue-600" id="total-productos">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold mb-2">Productos Bajos en Stock</h3>
                <p class="text-2xl text-red-600" id="productos-bajos">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold mb-2">Pedidos Pendientes</h3>
                <p class="text-2xl text-yellow-600" id="pedidos-pendientes">0</p>
            </div>
        </div>

        <!-- Sección de Productos -->
        <div id="seccion-productos" class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold">Gestión de Productos</h2>
                <button onclick="mostrarFormularioProducto()" 
                        class="bg-[#8B4513] text-white px-4 py-2 rounded hover:bg-amber-700 transition-colors">
                    Agregar Producto
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Nombre</th>
                            <th class="px-6 py-3 text-left">Stock Actual</th>
                            <th class="px-6 py-3 text-left">Stock Mínimo</th>
                            <th class="px-6 py-3 text-left">Unidad</th>
                            <th class="px-6 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-productos">
                        <!-- Los productos se cargarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección de Recetas -->
<div id="seccion-recetas" class="bg-white p-6 rounded-lg shadow-md mb-8 hidden">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Gestión de Recetas</h2>
        <button onclick="mostrarFormularioReceta()" 
                class="bg-[#8B4513] text-white px-4 py-2 rounded hover:bg-amber-700 transition-colors">
            Nueva Receta
        </button>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold mb-4">Recetas Disponibles</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Producto</th>
                        <th class="px-6 py-3 text-left">Ingredientes Necesarios</th>
                        <th class="px-6 py-3 text-left">Estado Stock</th>
                        <th class="px-6 py-3 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista-recetas">
                    <!-- Las recetas se cargarán aquí -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Formulario de Receta -->
<div id="modal-receta" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-2xl">
        <h3 class="text-xl font-bold mb-4">Configurar Receta</h3>
        <form id="form-receta" onsubmit="guardarReceta(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Producto del Menú</label>
                    <select id="producto-receta" name="id_producto" required
                            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2">
                    </select>
                </div>
            </div>

            <div>
                <h4 class="text-lg font-medium mb-2">Ingredientes Necesarios</h4>
                <div id="lista-ingredientes-receta" class="space-y-3">
                    <!-- Aquí se agregarán los ingredientes -->
                </div>
                <button type="button" onclick="agregarIngredienteAReceta()"
                        class="mt-3 text-sm text-blue-600 hover:text-blue-800">
                    + Agregar Ingrediente
                </button>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal('modal-receta')"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#8B4513] text-white rounded hover:bg-amber-700">
                    Guardar Receta
                </button>
            </div>
        </form>
    </div>


</div>

            
        <!-- Sección de Pedidos -->
<div id="seccion-pedidos" class="bg-white p-6 rounded-lg shadow-md mb-8 hidden">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Pedidos a Proveedores</h2>
        <button onclick="mostrarFormularioPedido()" 
                class="bg-[#8B4513] text-white px-4 py-2 rounded hover:bg-amber-700 transition-colors">
            Nuevo Pedido
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Proveedor</th>
                    <th class="px-6 py-3 text-left">Fecha</th>
                    <th class="px-6 py-3 text-left">Estado</th>
                    <th class="px-6 py-3 text-left">Total</th>
                    <th class="px-6 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="lista-pedidos">
                <!-- Los pedidos se cargarán aquí -->
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal para Formulario de Producto -->
    <div id="modal-producto" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Agregar/Editar Producto</h3>
            <form id="form-producto" onsubmit="guardarProducto(event)" class="space-y-4">
                <input type="hidden" name="action" value="save">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Producto</label>
                    <input type="text" id="nombre" name="nombre" required
                           placeholder="Ingrese nombre del producto"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-[#8B4513] focus:outline-none focus:ring-1 focus:ring-[#8B4513]">
                </div>
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Actual</label>
                    <input type="number" id="stock" name="stock" required min="0"
                           placeholder="Cantidad actual"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-[#8B4513] focus:outline-none focus:ring-1 focus:ring-[#8B4513]">
                </div>
                <div>
                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700">Stock Mínimo</label>
                    <input type="number" id="stock_minimo" name="stock_minimo" required min="0"
                           placeholder="Cantidad mínima permitida"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-[#8B4513] focus:outline-none focus:ring-1 focus:ring-[#8B4513]">
                </div>
                <div>
                    <label for="unidad" class="block text-sm font-medium text-gray-700">Unidad de Medida</label>
                    <input type="text" id="unidad" name="unidad" required
                           placeholder="Ej: kg, litros, unidades"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 focus:border-[#8B4513] focus:outline-none focus:ring-1 focus:ring-[#8B4513]">
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModal('modal-producto')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8B4513]">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#8B4513] border border-transparent rounded-md text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#8B4513]">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Formulario de Receta -->
    <div id="modal-receta" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <!-- Contenido del modal de recetas -->
    </div>

    <!-- Modal para Formulario de Pedido -->
<div id="modal-pedido" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-2xl">
        <h3 class="text-xl font-bold mb-4">Nuevo Pedido a Proveedor</h3>
        <form id="form-pedido" onsubmit="guardarPedido(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                    <select id="proveedor" name="id_proveedor" required
                            class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">Seleccione un proveedor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <select id="estado" name="estado" required
                            class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="pendiente">Pendiente</option>
                        <option value="en tránsito">En tránsito</option>
                        <option value="recibido">Recibido</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Productos</label>
                <div id="lista-productos-pedido" class="space-y-2">
                    <!-- Aquí se agregarán dinámicamente los productos -->
                </div>
                <button type="button" onclick="agregarProductoPedido()"
                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                    + Agregar Producto
                </button>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal('modal-pedido')"
                        class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#8B4513] text-white rounded hover:bg-amber-700">
                    Guardar Pedido
                </button>
            </div>
        </form>
    </div>
</div>



    


    

    <script src="js/bodega.js"></script>
</body>
</html>