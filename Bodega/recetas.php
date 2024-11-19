    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Recetas - Sistema Bodega</title>
        <link rel="stylesheet" href="css/recetas.css">
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

        <!-- Sección de Recetas -->
        <div class="seccion-recetas">
            <div class="recetas-header">
                <h2>Gestión de Recetas</h2>
                <button onclick="mostrarFormularioReceta()" class="btn-agregar-receta">Nueva Receta</button>
            </div>
            <div class="tabla-container">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Ingredientes</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="lista-recetas">
                        <!-- Las recetas se cargarán aquí -->
                    </tbody>
                </table>
            </div>
        </div>

       <!-- Modal para Nueva Receta -->
       <div id="modal-receta" class="modal hidden">
            <div class="modal-content">
                    <h2>Agregar Nueva Receta</h2>
                <form id="form-agregar-receta">
                    <div class="form-group">
                        <label for="producto">Producto</label>
                        <select id="producto" name="producto" required>
                            <option value="">Seleccione un producto</option>
                            <!-- Opciones se cargarán dinámicamente -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ingredientes</label>
                        <div id="lista-ingredientes">
                            <div class="ingrediente-item">
                                <select  name="ingredientes[]" class="ingrediente-select" required>
                                    <option value="">Seleccione un ingrediente</option>
                                </select>
                                <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" step="1" required>
                                <button type="button" class="btn-remove-ingrediente" onclick="eliminarIngrediente(this)">Eliminar</button>
                            </div>
                        </div>
                        <button type="button" id="btn-agregar-ingrediente" onclick="agregarIngrediente()">Agregar Ingrediente</button>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="cerrarModalReceta()">Cancelar</button>
                        <button type="submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Modal para Editar Receta -->
        <div id="modal-editar-receta" class="modal hidden">
            <div class="modal-content">
                <h2>Editar Receta</h2>
                <form id="form-editar-receta">
                    <div class="form-group">
                        <label for="editar-producto">Producto</label>
                        <!-- Deshabilitado porque el producto no debería cambiar -->
                        <select id="editar-producto" name="producto" disabled>
                            <option value="">Seleccione un producto</option>
                            <!-- Opciones llenadas dinámicamente -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ingredientes</label>
                        <div id="editar-lista-ingredientes">
                            <!-- Ingredientes dinámicos se cargarán aquí -->
                        </div>
                        <button type="button" id="btn-editar-agregar-ingrediente" onclick="agregarIngredienteEditar()">Agregar Ingrediente</button>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="cerrarModalEditarReceta()">Cancelar</button>
                        <button type="submit">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>

    


        <script src="js/recetas.js"></script>
    </body>
</html>