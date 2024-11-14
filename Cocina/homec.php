<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Cocina - Restaurante Siglo XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilo personalizado para el sistema de cocina */
        .bg-kitchen-dark {
            background-color: #f4f4f5; /* Fondo claro, casi blanco */
        }
        .bg-steel {
            background-color: #d1d5db; /* Color gris acero */
        }
        .text-steel-dark {
            color: #374151; /* Texto en gris oscuro */
        }
        .border-steel {
            border-color: #9ca3af; /* Borde de acero */
        }
        .bg-light-steel {
            background-color: #e5e7eb; /* Fondo claro estilo acero */
        }
    </style>
</head>
<body class="bg-kitchen-dark">
    <!-- Barra de Navegación -->
    <nav class="bg-steel text-steel-dark py-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6">
            <h1 class="text-3xl font-semibold">Sistema de Cocina</h1>
            <a href="../cerrar-sesion.php" class="hover:text-gray-600 text-lg">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Tablero de Pedidos -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Pedidos Pendientes -->
            <div class="bg-light-steel p-6 rounded-lg shadow-lg text-center border border-steel">
                <h3 class="text-lg font-bold text-red-600 mb-4">Pendientes</h3>
                <div id="pedidos-pendientes" class="space-y-4">
                    <!-- Los pedidos pendientes se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos En Preparación -->
            <div class="bg-light-steel p-6 rounded-lg shadow-lg text-center border border-steel">
                <h3 class="text-lg font-bold text-yellow-600 mb-4">En Preparación</h3>
                <div id="pedidos-preparacion" class="space-y-4">
                    <!-- Los pedidos en preparación se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos Listos para Entregar -->
            <div class="bg-light-steel p-6 rounded-lg shadow-lg text-center border border-steel">
                <h3 class="text-lg font-bold text-green-600 mb-4">Listos para Entregar</h3>
                <div id="pedidos-listos" class="space-y-4">
                    <!-- Los pedidos listos se cargarán aquí -->
                </div>
            </div>

           <!-- Pedidos Listos para Pagar -->
<div class="bg-light-steel p-6 rounded-lg shadow-lg text-center border border-steel">
    <h3 class="text-lg font-bold text-purple-600 mb-4">Listos para Pagar</h3>
    <div id="pedidos-listos-para-pagar" class="space-y-4">
        <!-- Los pedidos listos para pagar se cargarán aquí -->
    </div>
</div>

        </div>
    </div>

    <script src="js/cocina.js"></script>
</body>
</html>
