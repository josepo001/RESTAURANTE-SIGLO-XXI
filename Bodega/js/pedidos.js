// Array para almacenar los pedidos actuales
let pedidosActuales = [];

// Función para cargar los pedidos desde la API
async function cargarPedidos() {
    try {
        const respuesta = await fetch('api/obtener_pedidos.php'); // Reemplaza con tu API
        if (!respuesta.ok) throw new Error('Error al cargar los pedidos');

        const datos = await respuesta.json();
        console.log('Pedidos recibidos:', datos);

        if (datos.success) {
            pedidosActuales = datos.pedidos; // Guardar pedidos en memoria
            renderizarPedidos(); // Mostrar los pedidos en la tabla
        } else {
            mostrarNotificacion(datos.message, 'error');
        }
    } catch (error) {
        console.error('Error al cargar pedidos:', error);
        mostrarNotificacion('Error al cargar los pedidos.', 'error');
    }
}

// Función para renderizar los pedidos en la tabla
function renderizarPedidos() {
    const listaPedidos = document.getElementById('lista-pedidos');
    listaPedidos.innerHTML = ''; // Limpiar la tabla antes de llenarla

    pedidosActuales.forEach(pedido => {
        const fila = document.createElement('tr');
        const total = parseFloat(pedido.total) || 0; // Asegúrate de que el total sea un número

        fila.innerHTML = `
    <td>${pedido.proveedor_nombre}</td>
    <td>${pedido.fecha_pedido}</td>
    <td>${pedido.estado}</td>
    <td>${total.toFixed(2)}</td>
    <td>
        <button onclick="editarPedido(${pedido.id})" class="btn-accion bg-green">Editar</button>
        ${
            pedido.estado.toLowerCase() !== 'recibido'
            ? `<button onclick="eliminarPedido(${pedido.id})" class="btn-accion bg-brown">Eliminar</button>`
            : ''
        }
    </td>
`;

        listaPedidos.appendChild(fila);
    });
}



// Función para cargar los proveedores dinámicamente
async function cargarProveedores() {
    try {
        const respuesta = await fetch('api/obtener_proveedores.php');
        if (!respuesta.ok) throw new Error('Error al cargar proveedores');

        const datos = await respuesta.json();
        console.log('Proveedores recibidos:', datos); // Verificar en la consola

        const selectProveedor = document.getElementById('proveedor');
        selectProveedor.innerHTML = `<option value="">Seleccione un proveedor</option>`; // Reinicia las opciones

        if (datos.success) {
            datos.proveedores.forEach(proveedor => {
                const opcion = document.createElement('option');
                opcion.value = proveedor.id;
                opcion.textContent = proveedor.nombre;
                selectProveedor.appendChild(opcion);
            });
        } else {
            mostrarNotificacion(datos.message, 'error');
        }
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
        mostrarNotificacion('Error al cargar proveedores.', 'error');
    }
}

// Función para cargar los productos dinámicamente
async function cargarProductos() {
    try {
        const respuesta = await fetch('api/obtener_productos.php'); // Reemplaza con tu API
        if (!respuesta.ok) throw new Error('Error al cargar productos');

        const datos = await respuesta.json();
        console.log('Productos recibidos:', datos);

        if (datos.success) {
            const selects = document.querySelectorAll('.producto-select'); // Selecciona todos los selectores de producto
            selects.forEach(select => {
                select.innerHTML = `<option value="">Seleccione un producto</option>`; // Reinicia las opciones
                datos.productos.forEach(producto => {
                    const opcion = document.createElement('option');
                    opcion.value = producto.id;
                    opcion.textContent = producto.nombre;
                    select.appendChild(opcion);
                });
            });
        } else {
            mostrarNotificacion(datos.message, 'error');
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
        mostrarNotificacion('Error al cargar productos.', 'error');
    }
}

// Inicializar formulario para nuevo pedido
function inicializarFormularioNuevoPedido() {
    cargarProveedores(); // Cargar los proveedores en el selector
    cargarProductos(); // Cargar los productos dinámicamente
    abrirModal('modal-nuevo-pedido'); // Abrir el modal
}

// Función para agregar un nuevo campo de producto
function agregarProducto() {
    const contenedor = document.getElementById('productos-contenedor');
    const productoItem = document.createElement('div');
    productoItem.classList.add('producto-item');

    productoItem.innerHTML = `
        <select class="producto-select" name="producto[]" required>
            <option value="">Seleccione un producto</option>
        </select>
        <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
        <input type="number" name="precio_unitario[]" placeholder="Precio unitario" step="0.01" required>
        <button type="button" class="btn-remove-ingrediente" onclick="eliminarProducto(this)">Eliminar</button>
    `;

    contenedor.appendChild(productoItem);
    cargarProductos(); // Cargar productos en el nuevo selector
}

// Función para eliminar un campo de producto
function eliminarProducto(boton) {
    boton.parentElement.remove();
}

// Función para eliminar un pedido
async function eliminarPedido(id) {
    // Confirmación antes de proceder a eliminar
    if (!confirm('¿Estás seguro de que deseas eliminar este pedido?')) return;

    try {
        const respuesta = await fetch('api/eliminar_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id }) // Enviar el ID del pedido
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            alert('Pedido eliminado con éxito.');
            cargarPedidos(); // Recargar la tabla de pedidos
        } else {
            alert(`Error al eliminar el pedido: ${resultado.message}`);
        }
    } catch (error) {
        console.error('Error al eliminar el pedido:', error);
        alert('Ocurrió un error al intentar eliminar el pedido.');
    }
}


// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.getElementById('notificacion');
    notificacion.textContent = mensaje;

    notificacion.className = `notificacion ${tipo} mostrar`;

    setTimeout(() => {
        notificacion.className = 'notificacion hidden';
    }, 3000);
}

// Función para abrir un modal
function abrirModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden'); // Muestra el modal
}

// Función para cerrar un modal
function cerrarModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden'); // Oculta el modal
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    cargarPedidos(); // Cargar los pedidos en la tabla
    document.querySelector('.btn-agregar-pedido').addEventListener('click', inicializarFormularioNuevoPedido);
});

document.getElementById("form-nuevo-pedido").addEventListener("submit", async function (event) {
    event.preventDefault(); // Evitar que el formulario recargue la página

    const proveedor = document.getElementById("proveedor").value;
    const estado = document.getElementById("estado").value;

    // Obtener los productos del formulario
    const productos = Array.from(document.querySelectorAll("#productos-contenedor .producto-item")).map(item => ({
        id_producto: item.querySelector(".producto-select").value,
        cantidad: item.querySelector('input[name="cantidad[]"]').value,
        precio_unitario: item.querySelector('input[name="precio_unitario[]"]').value
    }));

    if (!proveedor || productos.length === 0 || productos.some(p => !p.id_producto || !p.cantidad || !p.precio_unitario)) {
        alert("Por favor, complete todos los campos correctamente.");
        return;
    }

    try {
        const respuesta = await fetch("api/guardar_pedido.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_proveedor: proveedor,
                estado: estado,
                productos: productos
            })
        });

        const resultado = await respuesta.json();
        if (resultado.success) {
            alert("Pedido guardado con éxito.");
            cerrarModal("modal-nuevo-pedido"); // Cerrar el modal
            cargarPedidos(); // Recargar la tabla de pedidos
        } else {
            alert(`Error al guardar el pedido: ${resultado.message}`);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Hubo un problema al guardar el pedido.");
    }
});
async function editarPedido(id) {
    const pedido = pedidosActuales.find(p => p.id === id);

    if (!pedido) {
        alert('Pedido no encontrado');
        return;
    }

    // Llenar los campos con la información del pedido
    document.getElementById('editar-proveedor').value = pedido.id_proveedor;
    document.getElementById('editar-estado').value = pedido.estado;

    // Limpiar y llenar el contenedor de productos
    const productosContenedor = document.getElementById('editar-productos-contenedor');
    productosContenedor.innerHTML = '';

    pedido.productos.forEach(producto => {
        const productoItem = document.createElement('div');
        productoItem.classList.add('producto-item');

        productoItem.innerHTML = `
            <select class="producto-select" name="producto[]" required>
                <option value="${producto.id_producto}" selected>${producto.nombre}</option>
                <!-- Opciones adicionales se cargarán después -->
            </select>
            <input type="number" name="cantidad[]" placeholder="Cantidad" value="${producto.cantidad}" min="1" required>
            <input type="number" name="precio_unitario[]" placeholder="Precio unitario" value="${producto.precio_unitario}" step="0.01" required>
            <button type="button" class="btn-remove-ingrediente" onclick="eliminarProducto(this)">Eliminar</button>
        `;
        productosContenedor.appendChild(productoItem);
    });

    // Cargar productos disponibles en los selects
    await cargarProductos();

    abrirModal('modal-editar-pedido'); // Abrir el modal
}
function agregarProductoEditar() {
    const contenedor = document.getElementById('editar-productos-contenedor');
    const productoItem = document.createElement('div');
    productoItem.classList.add('producto-item');

    productoItem.innerHTML = `
        <select class="producto-select" name="producto[]" required>
            <option value="">Seleccione un producto</option>
        </select>
        <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" required>
        <input type="number" name="precio_unitario[]" placeholder="Precio unitario" step="0.01" required>
        <button type="button" class="btn-remove-ingrediente" onclick="eliminarProducto(this)">Eliminar</button>
    `;

    contenedor.appendChild(productoItem);
    cargarProductos(); // Cargar productos en el nuevo selector
}
document.getElementById('form-editar-pedido').addEventListener('submit', async function (event) {
    event.preventDefault(); // Evitar recargar la página

    const id = pedidosActuales.find(p => p.id === parseInt(document.getElementById('pedido-id').value));
    const proveedor = document.getElementById('editar-proveedor').value;
    const estado = document.getElementById('editar-estado').value;

    const productos = Array.from(document.querySelectorAll('#editar-productos-contenedor .producto-item')).map(item => ({
        id_producto: item.querySelector('.producto-select').value,
        cantidad: item.querySelector('input[name="cantidad[]"]').value,
        precio_unitario: item.querySelector('input[name="precio_unitario[]"]').value
    }));

    if (!proveedor || productos.length === 0 || productos.some(p => !p.id_producto || !p.cantidad || !p.precio_unitario)) {
        alert('Por favor, complete todos los campos correctamente.');
        return;
    }

    try {
        const respuesta = await fetch('api/guardar_pedido.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                id_proveedor: proveedor,
                estado: estado,
                productos: productos
            })
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            alert('Pedido actualizado con éxito.');
            cerrarModal('modal-editar-pedido'); // Cerrar el modal
            cargarPedidos(); // Recargar la tabla
        } else {
            alert(`Error al actualizar el pedido: ${resultado.message}`);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Hubo un problema al actualizar el pedido.');
    }
});
