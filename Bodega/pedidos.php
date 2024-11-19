    <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestión de Pedidos - Sistema Bodega</title>
            <link rel="stylesheet" href="css/pedidos.css">
        </head>
        <body>
            <div id="notificacion" class="notificacion hidden"></div>

            <!-- Barra de Navegación -->
            <nav class="nav-bar">
                <div class="nav-title">Sistema Bodega</div>
                <div class="nav-links">
                    <a href="homeb.php" class="nav-link">Productos</a>
                    <a href="recetas.php" class="nav-link">Recetas</a>
                    <a href="pedidos.php" class="nav-link">Pedidos Proveedores</a>
                    <a href="../cerrar-sesion.php" class="nav-link">Cerrar Sesión</a>
                </div>
            </nav>

            <!-- Sección de Pedidos -->
            <div class="seccion-pedidos">
                <div class="pedidos-header">
                    <h2>Pedidos Proveedor</h2>
                    <button onclick="inicializarFormularioNuevoPedido()" class="btn-agregar-pedido">Nuevo Pedido</button>
                </div>
                <div class="tabla-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Proveedor</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="lista-pedidos">
                            <!-- Los pedidos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        <!-- Modal Nuevo Pedido -->
        <div id="modal-nuevo-pedido" class="modal hidden">
            <div class="modal-content">
                <h2>Nuevo Pedido a Proveedor</h2>
                <form id="form-nuevo-pedido">
                    <!-- Proveedor -->
                    <div class="form-group">
                        <label for="proveedor">Proveedor:</label>
                        <select id="proveedor" name="proveedor" required>
                            <option value="">Seleccione un proveedor</option>
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select id="estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                    <!-- Productos -->
                    <div id="productos-contenedor">
                        <label>Productos:</label>
                        <div class="producto-item">
                            <select class="producto-select" name="producto[]" required>
                                <option value="">Seleccione un producto</option>
                                <!-- Opciones dinámicas -->
                            </select>
                            <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
                            <input type="number" name="precio_unitario[]" placeholder="Precio unitario" step="1" required>
                            <button type="button" class="btn-remove-ingrediente" onclick="eliminarProducto(this)">Eliminar</button>
                        </div>
                    </div>
                    <button type="button" id="btn-agregar-producto" class="btn-agregar-receta" onclick="agregarProducto()">+ Agregar Producto</button>
                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="button" onclick="cerrarModal('modal-nuevo-pedido')">Cancelar</button>
                        <button type="submit">Guardar Pedido</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Editar Pedido -->
        <div id="modal-editar-pedido" class="modal hidden">
            <div class="modal-content">
                <h2>Editar Pedido a Proveedor</h2>
                <form id="form-editar-pedido">
                    <!-- Proveedor -->
                    <div class="form-group">
                        <label for="editar-proveedor">Proveedor:</label>
                        <select id="editar-proveedor" name="proveedor" required>
                            <option value="">Seleccione un proveedor</option>
                            <!-- Opciones dinámicas -->
                        </select>
                    </div>
                    <!-- Estado -->
                    <div class="form-group">
                        <label for="editar-estado">Estado:</label>
                        <select id="editar-estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en tránsito">En tránsito</option>
                            <option value="recibido">Recibido</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <!-- Productos -->
                    <div id="editar-productos-contenedor">
                        <label>Productos:</label>
                        <!-- Los productos se llenarán dinámicamente con JavaScript -->
                    </div>
                    <button type="button" id="btn-editar-agregar-producto" onclick="agregarProductoEditar()">+ Agregar Producto</button>
                    <!-- Acciones -->
                    <div class="form-actions">
                        <button type="button" onclick="cerrarModal('modal-editar-pedido')">Cancelar</button>
                        <button type="submit">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>


        <script src="js/pedidos.js"></script>
    </body>
</html>