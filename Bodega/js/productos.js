// Almacén temporal para los productos cargados
let productosActuales = [];

// Función para cargar los productos desde la API y mostrarlos en la tabla
async function cargarProductos() {
    try {
        const respuesta = await fetch('api/obtener_productos.php');
        if (!respuesta.ok) throw new Error('Error al obtener los productos');
        const datos = await respuesta.json();
        
        if (datos.success) {
            productosActuales = datos.productos; // Guardar productos cargados
            renderizarProductos(productosActuales); // Mostrar productos en la tabla
        } else {
            throw new Error(datos.message || 'Error desconocido al cargar los productos');
        }
    } catch (error) {
        mostrarNotificacion(`Error: ${error.message}`, 'error');
    }
}

// Función para mostrar los productos en la tabla
function renderizarProductos(productos) {
    const listaProductos = document.getElementById('lista-productos');
    listaProductos.innerHTML = ''; // Limpiar tabla

    productos.forEach(producto => {
        const fila = document.createElement('tr');

        // Determinar la clase del color en función del stock
        let stockColorClass = '';
        if (producto.stock > producto.stock_minimo) {
            stockColorClass = 'text-green'; // Sobre el stock mínimo
        } else if (producto.stock === producto.stock_minimo) {
            stockColorClass = 'text-yellow'; // Igual al stock mínimo
        } else {
            stockColorClass = 'text-red'; // Por debajo del stock mínimo
        }

        fila.innerHTML = `
            <td>${producto.nombre}</td>
            <td class="${stockColorClass}">${producto.stock}</td>
            <td>${producto.stock_minimo}</td>
            <td>${producto.unidad}</td>
            <td>
                <button onclick="mostrarFormularioEditar(${producto.id})" class="btn-accion bg-green">Editar</button>
                <button onclick="eliminarProducto(${producto.id})" class="btn-accion bg-brown">Eliminar</button>
            </td>
        `;
        listaProductos.appendChild(fila);
    });
}



// Función para mostrar el formulario de agregar producto
function mostrarFormularioAgregar() {
    document.getElementById('modal-agregar').classList.remove('hidden'); // Mostrar modal de agregar
}

// Función para cerrar el formulario de agregar producto
function cerrarModalAgregar() {
    document.getElementById('modal-agregar').classList.add('hidden'); // Ocultar modal de agregar
    document.getElementById('form-agregar-producto').reset(); // Limpiar formulario
}

// Función para enviar el formulario de agregar producto
document.getElementById('form-agregar-producto').addEventListener('submit', async function (e) {
    e.preventDefault();

    const nombre = document.getElementById('nombre').value;
    const stock = document.getElementById('stock').value;
    const stock_minimo = document.getElementById('stock_minimo').value;
    const unidad = document.getElementById('unidad').value;

    try {
        const respuesta = await fetch('api/guardar_producto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre, stock, stock_minimo, unidad })
        });

        const datos = await respuesta.json();
        if (datos.success) {
            mostrarNotificacion(datos.message, 'success');
            cerrarModalAgregar(); // Cerrar el modal de agregar
            cargarProductos(); // Recargar los productos
        } else {
            throw new Error(datos.message);
        }
    } catch (error) {
        mostrarNotificacion(`Error: ${error.message}`, 'error');
    }
});

// Función para mostrar el formulario de editar producto
function mostrarFormularioEditar(idProducto) {
    const producto = productosActuales.find(p => p.id === idProducto);
    if (!producto) return mostrarNotificacion('Producto no encontrado', 'error');

    // Mostrar datos en el formulario de edición (puedes adaptar los IDs a tu HTML)
    document.getElementById('modal-editar').classList.remove('hidden');
    document.getElementById('editar-id').value = producto.id;
    document.getElementById('editar-nombre').value = producto.nombre;
    document.getElementById('editar-stock').value = producto.stock;
    document.getElementById('editar-stock_minimo').value = producto.stock_minimo;
    document.getElementById('editar-unidad').value = producto.unidad;
}

// Función para cerrar el formulario de editar producto
function cerrarModalEditar() {
    document.getElementById('modal-editar').classList.add('hidden');
    document.getElementById('form-editar-producto').reset();
}

// Función para enviar el formulario de editar producto
document.getElementById('form-editar-producto').addEventListener('submit', async function (e) {
    e.preventDefault();

    const id = document.getElementById('editar-id').value;
    const nombre = document.getElementById('editar-nombre').value;
    const stock = document.getElementById('editar-stock').value;
    const stock_minimo = document.getElementById('editar-stock_minimo').value;
    const unidad = document.getElementById('editar-unidad').value;

    try {
        const respuesta = await fetch('api/guardar_producto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, nombre, stock, stock_minimo, unidad })
        });

        const datos = await respuesta.json();
        if (datos.success) {
            mostrarNotificacion(datos.message, 'success');
            cerrarModalEditar(); // Cerrar el modal de edición
            cargarProductos(); // Recargar los productos
        } else {
            throw new Error(datos.message);
        }
    } catch (error) {
        mostrarNotificacion(`Error: ${error.message}`, 'error');
    }
});

// Función para eliminar un producto
async function eliminarProducto(idProducto) {
    try {
        // Confirmar la acción antes de eliminar
        const confirmacion = window.confirm('¿Estás seguro de que deseas eliminar este producto?');
        if (!confirmacion) return; // Si el usuario cancela, no hacemos nada

        const id = parseInt(idProducto, 10);

        if (isNaN(id) || id <= 0) {
            throw new Error('La ID del producto no es válida');
        }

        const respuesta = await fetch(`api/eliminar_producto.php?id=${id}`, {
            method: 'DELETE',
        });

        const datos = await respuesta.json();

        if (datos.success) {
            mostrarNotificacion('Producto eliminado correctamente', 'success'); // Notificación de éxito
            cargarProductos(); // Recargar los productos después de eliminar
        } else {
            throw new Error(datos.message || 'Error desconocido al eliminar el producto');
        }
    } catch (error) {
        mostrarNotificacion(`Error: ${error.message}`, 'error');
    }
}




function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.getElementById('notificacion');
    notificacion.textContent = mensaje; // Agregar el mensaje
    notificacion.className = `notificacion ${tipo} mostrar`; // Agregar clases dinámicas

    // Ocultar automáticamente después de 3 segundos
    setTimeout(() => {
        notificacion.className = 'notificacion'; // Eliminar las clases de visibilidad
    }, 3000);
}


// Cargar productos al cargar la página
document.addEventListener('DOMContentLoaded', cargarProductos);
