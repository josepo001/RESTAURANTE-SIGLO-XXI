<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Finanzas - Restaurante Siglo XXI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos personalizados */
        .bg-finance {
            background-color: #f9fafb; /* Fondo claro */
        }
        .bg-card-finance {
            background-color: #ffffff; /* Fondo blanco para claridad */
        }
        .text-finance-dark {
            color: #333; /* Texto en gris oscuro */
        }
        .shadow-light {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Sombra suave */
        }
        .border-blue-accent {
            border-color: #3b82f6; /* Azul para detalles */
        }
    </style>
</head>
<body class="bg-finance">
    <!-- Barra de Navegación -->
    <nav class="bg-[#3b82f6] text-white py-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6">
            <h1 class="text-3xl font-semibold">Sistema Finanzas</h1>
            <a href="../cerrar-sesion.php" class="hover:text-gray-300 text-lg">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-12">
        <!-- Resumen Diario -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-card-finance p-6 rounded-lg shadow-light text-center border border-gray-200">
                <h3 class="text-lg font-semibold text-finance-dark mb-2">Ingresos Hoy</h3>
                <p class="text-3xl font-bold text-green-600" id="ingresos-hoy">$0.00</p>
            </div>
            <div class="bg-card-finance p-6 rounded-lg shadow-light text-center border border-gray-200">
                <h3 class="text-lg font-semibold text-finance-dark mb-2">Egresos Hoy</h3>
                <p class="text-3xl font-bold text-red-600" id="egresos-hoy">$0.00</p>
            </div>
            <div class="bg-card-finance p-6 rounded-lg shadow-light text-center border border-gray-200">
                <h3 class="text-lg font-semibold text-finance-dark mb-2">Utilidad Hoy</h3>
                <p class="text-3xl font-bold text-blue-600" id="utilidad-hoy">$0.00</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <div class="bg-card-finance p-6 rounded-lg shadow-light">
                <h3 class="text-lg font-semibold text-finance-dark mb-4">Ventas del Mes</h3>
                <canvas id="grafico-ventas"></canvas>
            </div>
            <div class="bg-card-finance p-6 rounded-lg shadow-light">
                <h3 class="text-lg font-semibold text-finance-dark mb-4">Utilidad Mensual</h3>
                <canvas id="grafico-utilidad"></canvas>
            </div>
        </div>

        <!-- Registro de Egresos -->
        <div class="bg-card-finance p-6 rounded-lg shadow-light mb-12">
            <h3 class="text-lg font-semibold text-finance-dark mb-4">Registrar Egreso</h3>
            <form id="form-egreso" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Concepto</label>
                    <input type="text" name="concepto" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monto</label>
                    <input type="number" name="monto" step="0.01" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <input type="date" name="fecha" required
                           class="w-full rounded-md border border-gray-300 px-3 py-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="bg-[#3b82f6] text-white px-4 py-2 rounded hover:bg-blue-700">
                        Registrar Egreso
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="bg-card-finance p-6 rounded-lg shadow-light">
            <h3 class="text-lg font-semibold text-finance-dark mb-4">Movimientos Recientes</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-finance-dark">Fecha</th>
                            <th class="px-6 py-3 font-semibold text-finance-dark">Tipo</th>
                            <th class="px-6 py-3 font-semibold text-finance-dark">Concepto</th>
                            <th class="px-6 py-3 font-semibold text-finance-dark">Monto</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-movimientos">
                        <!-- Los movimientos se cargarán dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Cargar datos iniciales
        function cargarDatos() {
            fetch('obtener_datos_financieros.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('ingresos-hoy').textContent = `$${data.ingresosHoy}`;
                    document.getElementById('egresos-hoy').textContent = `$${data.egresosHoy}`;
                    document.getElementById('utilidad-hoy').textContent = `$${data.utilidadHoy}`;
                    
                    // Actualizar gráficos
                    actualizarGraficos(data.graficos);
                    
                    // Actualizar tabla de movimientos
                    actualizarTablaMovimientos(data.movimientos);
                });
        }

        // Inicializar la página
        document.addEventListener('DOMContentLoaded', () => {
            cargarDatos();

            // Manejar formulario de egresos
            document.getElementById('form-egreso').addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                
                fetch('registrar_egreso.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Egreso registrado correctamente');
                        cargarDatos();
                        e.target.reset();
                    } else {
                        alert('Error al registrar el egreso');
                    }
                });
        });

        function actualizarGraficos(datos) {
            // Configuración de Chart.js
            const ctxVentas = document.getElementById('grafico-ventas').getContext('2d');
            const ctxUtilidad = document.getElementById('grafico-utilidad').getContext('2d');

            new Chart(ctxVentas, {
                type: 'bar',
                data: datos.ventas,
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            new Chart(ctxUtilidad, {
                type: 'line',
                data: datos.utilidad,
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        function actualizarTablaMovimientos(movimientos) {
            const tabla = document.getElementById('tabla-movimientos');
            tabla.innerHTML = movimientos.map(mov => `
                <tr class="border-b">
                    <td class="px-6 py-4">${mov.fecha}</td>
                    <td class="px-6 py-4">${mov.tipo}</td>
                    <td class="px-6 py-4">${mov.concepto}</td>
                    <td class="px-6 py-4 ${mov.tipo === 'ingreso' ? 'text-green-600' : 'text-red-600'}">
                        $${mov.monto}
                    </td>
                </tr>
            `).join('');
        }
    </script>
    <script src="js/finanzas.js"></script>
</body>
</html>
