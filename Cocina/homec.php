<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Cocina - Restaurante Siglo XXI</title>
    <link rel="stylesheet" href="css/homec.css">
</head>
<body>
    <!-- Barra de Navegación -->
    <nav>
        <div class="container">
            <h1>Sistema de Cocina</h1>
            <a href="../cerrar-sesion.php">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <!-- Tablero de Pedidos -->
        <div class="grid">
            <!-- Pedidos Pendientes -->
            <div class="card-pedido">
                <h3 class="text-rojo">Pendientes</h3>
                <div id="pedidos-pendientes">
                    <!-- Los pedidos pendientes se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos En Preparación -->
            <div class="card-pedido">
                <h3 class="text-amarillo">En Preparación</h3>
                <div id="pedidos-preparacion">
                    <!-- Los pedidos en preparación se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos Listos para Entregar -->
            <div class="card-pedido">
                <h3 class="text-verde">Listos para Entregar</h3>
                <div id="pedidos-listos">
                    <!-- Los pedidos listos se cargarán aquí -->
                </div>
            </div>

            <!-- Pedidos Listos para Pagar -->
            <div class="card-pedido">
                <h3 class="text-morado">Listos para Pagar</h3>
                <div id="pedidos-listos-para-pagar">
                    <!-- Los pedidos listos para pagar se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>

    <script src="js/cocina.js"></script>
</body>
</html>
