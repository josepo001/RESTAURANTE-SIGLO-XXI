<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Cocina - Restaurante Siglo XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-[#8B4513] text-white py-4 px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-serif">Sistema Cocina</h1>
            <a href="../cerrar-sesion.php" class="hover:text-gray-300">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Tablero de Pedidos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Pedidos Pendientes -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold mb-4 text-red-600">Pendientes</h3>
                <div id="pedidos-pendientes" class="space-y-4">
                    <!-- Los pedidos pendientes se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos En Preparación -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold mb-4 text-yellow-600">En Preparación</h3>
                <div id="pedidos-preparacion" class="space-y-4">
                    <!-- Los pedidos en preparación se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos Listos -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-bold mb-4 text-green-600">Listos para Entregar</h3>
                <div id="pedidos-listos" class="space-y-4">
                    <!-- Los pedidos listos se cargarán aquí -->
                </div>
            </div>
        </div>

        <!-- Detalles del Pedido Seleccionado -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-lg font-bold mb-4">Detalles del Pedido</h3>
            <div id="detalle-pedido" class="space-y-4">
                <!-- Los detalles del pedido seleccionado se mostrarán aquí -->
            </div>
        </div>
    </div>

    <script src="js/cocina.js"></script>
</body>
</html>