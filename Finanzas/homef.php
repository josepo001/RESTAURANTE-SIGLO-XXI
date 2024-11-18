<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Finanzas - Restaurante Siglo XXI</title>
    <link rel="stylesheet" href="css/homef.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <!-- Barra de Navegación -->
    <nav>
        <div class="container">
            <h1>Sistema Finanzas</h1>
            <a href="../cerrar-sesion.php">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <!-- Resumen Diario -->
        <div class="grid grid-3-cols">
            <div class="card">
                <h3>Ingresos Hoy</h3>
                <p class="text-green" id="ingresos-hoy">$0</p>
            </div>
            <div class="card">
                <h3>Egresos Hoy</h3>
                <p class="text-red" id="egresos-hoy">$0</p>
            </div>
            <div class="card">
                <h3>Utilidad Hoy</h3>
                <p class="text-blue" id="utilidad-hoy">$0</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-2-cols">
            <div class="card">
                <h3>Ventas del Mes</h3>
                <canvas id="grafico-ventas"></canvas>
            </div>
            <div class="card">
                <h3>Utilidad Mensual</h3>
                <canvas id="grafico-utilidad"></canvas>
            </div>
        </div>

        <!-- Registro de Egresos -->
        <div class="card">
            <h3>Registrar Egreso</h3>
            <form id="form-egreso">
                <div>
                    <label>Concepto</label>
                    <input type="text" name="concepto" required>
                </div>
                <div>
                    <label>Monto</label>
                    <input type="number" name="monto" step="1" required>
                </div>
                <div>
                    <label>Fecha</label>
                    <input type="date" name="fecha" required>
                </div>
                <div>
                    <button type="submit">Registrar Egreso</button>
                </div>
            </form>
        </div>

        <!-- Tabla de Movimientos -->
        <div class="card">
            <h3>Movimientos Recientes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody id="tabla-movimientos">
                    <!-- Datos dinámicos -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/finanzas.js"></script>
</body>
</html>
