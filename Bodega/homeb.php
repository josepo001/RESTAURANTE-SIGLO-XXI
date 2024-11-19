<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Sistema Bodega</title>
    <!-- Vinculación del CSS -->
    <link rel="stylesheet" href="css/homeb.css">
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

    <!-- Sección de Productos -->
    <div class="seccion-productos">
        <div class="productos-header">
            <h2>Gestión de Productos</h2>
            <button onclick="mostrarFormularioAgregar()" class="btn-agregar-producto">Agregar Producto</button>
        </div>
        <div class="tabla-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Stock Actual</th>
                        <th>Stock Mínimo</th>
                        <th>Unidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista-productos">
                    <!-- Los productos se cargarán aquí -->
                </tbody>
            </table>
        </div>
    </div>

   <!-- Modal para Agregar Producto -->
    <div id="modal-agregar" class="modal hidden">
        <div class="modal-content">
            <h2>Agregar Producto</h2>
            <form id="form-agregar-producto">
                <div class="form-group">
                    <label for="nombre">Nombre del Producto<span class="required">*</span></label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. Aderezo Cesar" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock<span class="required">*</span></label>
                    <input type="number" id="stock" name="stock" placeholder="Ej. 20" required>
                </div>
                <div class="form-group">
                    <label for="stock_minimo">Stock Mínimo<span class="required">*</span></label>
                    <input type="number" id="stock_minimo" name="stock_minimo" placeholder="Ej. 10" required>
                </div>
                <div class="form-group">
                    <label for="unidad">Unidad<span class="required">*</span></label>
                    <input type="text" id="unidad" name="unidad" placeholder="Ej. 237ml" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secundario" onclick="cerrarModalAgregar()">Cancelar</button>
                    <button type="submit" class="btn-principal">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Producto -->
    <div id="modal-editar" class="modal hidden">
        <div class="modal-content">
            <h2>Editar Producto</h2>
            <form id="form-editar-producto">
                <input type="hidden" id="editar-id" name="editar-id">
                <div class="form-group">
                    <label for="editar-nombre">Nombre del Producto<span class="required">*</span></label>
                    <input type="text" id="editar-nombre" name="editar-nombre" placeholder="Ej. Aderezo Cesar" required>
                </div>
                <div class="form-group">
                    <label for="editar-stock">Stock<span class="required">*</span></label>
                    <input type="number" id="editar-stock" name="editar-stock" placeholder="Ej. 20" required>
                </div>
                <div class="form-group">
                    <label for="editar-stock_minimo">Stock Mínimo<span class="required">*</span></label>
                    <input type="number" id="editar-stock_minimo" name="editar-stock_minimo" placeholder="Ej. 10" required>
                </div>
                <div class="form-group">
                    <label for="editar-unidad">Unidad<span class="required">*</span></label>
                    <input type="text" id="editar-unidad" name="editar-unidad" placeholder="Ej. 237ml" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secundario" onclick="cerrarModalEditar()">Cancelar</button>
                    <button type="submit" class="btn-principal">Guardar</button>
                </div>
            </form>
        </div>
    </div>


    <script src="js/productos.js"></script>
</body>
</html>
