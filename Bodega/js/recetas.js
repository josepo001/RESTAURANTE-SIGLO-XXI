let recetasActuales = [];
let ingredientesDisponibles = [];
let productosDisponibles = [];

// Función para cargar las recetas
async function cargarRecetas() {
    try {
        const respuesta = await fetch('api/obtener_recetas.php');
        if (!respuesta.ok) throw new Error('Error al cargar las recetas');

        const datos = await respuesta.json();
        if (datos.success) {
            recetasActuales = datos.recetas;
            renderizarRecetas();
        } else {
            mostrarNotificacion(datos.message || 'Error al cargar las recetas', 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error al cargar las recetas', 'error');
        console.error('Error al cargar las recetas:', error);
    }
}

// Función para renderizar recetas en la tabla
function renderizarRecetas() {
    const listaRecetas = document.getElementById('lista-recetas');
    listaRecetas.innerHTML = '';

    recetasActuales.forEach(receta => {
        const ingredientes = receta.ingredientes
            .map(ingrediente => 
                `${ingrediente.nombre} (${ingrediente.cantidad} ${ingrediente.unidad})`
            ).join(', ');

        const fila = document.createElement('tr');

        fila.innerHTML = `
    <td>${receta.nombre_producto}</td>
    <td>${ingredientes}</td>
    <td>${receta.ingredientes.every(i => i.stock_actual >= i.stock_minimo) ? 'Disponible' : 'Falta Stock'}</td>
    <td>
        <button onclick="mostrarFormularioEditarReceta(${receta.id_producto})" class="btn-accion bg-green">Editar</button>
        <button onclick="confirmarEliminarReceta(${receta.id_producto})" class="btn-accion bg-brown">Eliminar</button>
    </td>
`;


        listaRecetas.appendChild(fila);
    });
}

// Función para cargar los ingredientes disponibles
async function cargarIngredientes() {
    try {
        const respuesta = await fetch('api/obtener_ingredientes.php');
        if (!respuesta.ok) throw new Error('Error al cargar los ingredientes');

        const datos = await respuesta.json();

        if (datos.success && Array.isArray(datos.ingredientes)) {
            ingredientesDisponibles = datos.ingredientes;
        } else {
            throw new Error('La respuesta de los ingredientes no es válida');
        }
    } catch (error) {
        mostrarNotificacion('Error al cargar los ingredientes', 'error');
        console.error('Error al cargar los ingredientes:', error);
        ingredientesDisponibles = [];
    }
}

// Función para cargar los productos existentes
async function cargarProductos() {
    try {
        const respuesta = await fetch('api/obtener_productos_menu.php');
        if (!respuesta.ok) throw new Error('Error al cargar los productos');

        const datos = await respuesta.json();

        if (datos.success && Array.isArray(datos.products)) {
            productosDisponibles = datos.products;
            const selectProducto = document.getElementById('producto');
            datos.products.forEach(producto => {
                const opcion = document.createElement('option');
                opcion.value = producto.id;
                opcion.textContent = producto.nombre;
                selectProducto.appendChild(opcion);
            });
        } else {
            throw new Error('La respuesta de los productos no es válida');
        }
    } catch (error) {
        mostrarNotificacion('Error al cargar los productos', 'error');
        console.error('Error al cargar los productos:', error);
    }
}

// Función para mostrar el formulario de agregar receta
function mostrarFormularioReceta() {
    const modalReceta = document.getElementById('modal-receta');
    modalReceta.classList.remove('hidden');
}

// Función para mostrar el formulario de editar receta
function mostrarFormularioEditarReceta(idProducto) {
    const receta = recetasActuales.find(r => r.id_producto === idProducto);

    if (!receta) {
        mostrarNotificacion('Error al cargar la receta para editar', 'error');
        return;
    }

    const modalEditarReceta = document.getElementById('modal-editar-receta');
    const selectProducto = document.getElementById('editar-producto');
    const listaIngredientes = document.getElementById('editar-lista-ingredientes');

    selectProducto.innerHTML = `<option value="${receta.id_producto}" selected>${receta.nombre_producto}</option>`;
    selectProducto.disabled = true; // No permitir cambiar el producto al editar

    listaIngredientes.innerHTML = '';

    receta.ingredientes.forEach(ingrediente => {
        const ingredienteItem = document.createElement('div');
        ingredienteItem.classList.add('ingrediente-item');

        ingredienteItem.innerHTML = `
            <select name="ingredientes[]" class="ingrediente-select" required>
                <option value="">Seleccione un ingrediente</option>
                ${ingredientesDisponibles.map(ing => `
                    <option value="${ing.id}" ${ing.id === ingrediente.id ? 'selected' : ''}>${ing.nombre}</option>
                `).join('')}
            </select>
            <input type="number" name="cantidad[]" value="${ingrediente.cantidad}" placeholder="Cantidad" min="1" step="1" required>
            <button type="button" class="btn-remove-ingrediente" onclick="eliminarIngrediente(this)">Eliminar</button>
        `;
        listaIngredientes.appendChild(ingredienteItem);
    });

    modalEditarReceta.classList.remove('hidden');
}

// Función para cerrar el formulario de agregar receta
function cerrarModalReceta() {
    const modalReceta = document.getElementById('modal-receta');
    modalReceta.classList.add('hidden');
    document.getElementById('form-agregar-receta').reset();
}

// Función para cerrar el formulario de editar receta
function cerrarModalEditarReceta() {
    const modalEditarReceta = document.getElementById('modal-editar-receta');
    modalEditarReceta.classList.add('hidden');
}

// Función para guardar o actualizar receta
async function guardarReceta(event) {
    event.preventDefault();

    const isEditing = event.target.id === 'form-editar-receta'; // Verificar si es formulario de edición
    const idProducto = isEditing
        ? document.getElementById('editar-producto').value // Formulario de edición
        : document.getElementById('producto').value; // Formulario de creación

    const ingredientes = [];
    const listaIngredientes = isEditing
        ? document.querySelectorAll('#editar-lista-ingredientes .ingrediente-item') // Lista en formulario de edición
        : document.querySelectorAll('#lista-ingredientes .ingrediente-item'); // Lista en formulario de creación

    listaIngredientes.forEach(item => {
        const idIngrediente = item.querySelector('.ingrediente-select').value;
        const cantidad = item.querySelector('input[name="cantidad[]"]').value;

        if (idIngrediente && cantidad) {
            ingredientes.push({ id: idIngrediente, cantidad: cantidad });
        }
    });

    if (!idProducto || ingredientes.length === 0) {
        mostrarNotificacion('Por favor, completa todos los campos', 'error');
        return;
    }

    const datos = {
        id_producto: idProducto,
        ingredientes: ingredientes
    };

    try {
        const respuesta = await fetch('api/guardar_receta.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            mostrarNotificacion('Receta guardada correctamente', 'success');
            cerrarModalReceta();
            cerrarModalEditarReceta();
            cargarRecetas();
        } else {
            mostrarNotificacion(resultado.message || 'Error al guardar la receta', 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error al guardar la receta', 'error');
        console.error('Error al guardar la receta:', error);
    }
}


// Función para confirmar la eliminación de una receta
function confirmarEliminarReceta(idProducto) {
    if (confirm('¿Estás seguro de que deseas eliminar esta receta?')) {
        eliminarReceta(idProducto);
    }
}

// Función para eliminar una receta
async function eliminarReceta(idProducto) {
    try {
        const respuesta = await fetch(`api/eliminar_receta.php?id_producto=${idProducto}`, {
            method: 'DELETE'
        });

        const resultado = await respuesta.json();

        if (resultado.success) {
            mostrarNotificacion('Receta eliminada correctamente', 'success');
            cargarRecetas();
        } else {
            mostrarNotificacion(resultado.message || 'Error al eliminar la receta', 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error al eliminar la receta', 'error');
        console.error('Error al eliminar la receta:', error);
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.getElementById('notificacion');
    notificacion.textContent = mensaje;

    notificacion.className = `notificacion ${tipo} mostrar`;

    setTimeout(() => {
        notificacion.className = 'notificacion';
    }, 3000);
}

// Función para agregar un nuevo campo de ingrediente
function agregarIngrediente(modal = 'modal-receta') {
    const listaIngredientes = document.querySelector(`#${modal} .lista-ingredientes`);
    const ingredienteItem = document.createElement('div');
    ingredienteItem.classList.add('ingrediente-item');

    ingredienteItem.innerHTML = `
        <select name="ingredientes[]" class="ingrediente-select" required>
            <option value="">Seleccione un ingrediente</option>
            ${ingredientesDisponibles.map(ingrediente => `
                <option value="${ingrediente.id}">${ingrediente.nombre}</option>
            `).join('')}
        </select>
        <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" step="1" required>
        <button type="button" class="btn-remove-ingrediente" onclick="eliminarIngrediente(this)">Eliminar</button>
    `;

    listaIngredientes.appendChild(ingredienteItem);
}

// Función para eliminar un campo de ingrediente
function eliminarIngrediente(boton) {
    const ingredienteItem = boton.parentElement;
    ingredienteItem.remove();
}

// Inicializar la aplicación
document.addEventListener('DOMContentLoaded', () => {
    cargarRecetas();
    cargarIngredientes();
    cargarProductos();

    document.getElementById('form-agregar-receta').addEventListener('submit', guardarReceta);
    document.getElementById('form-editar-receta').addEventListener('submit', guardarReceta);

    document.querySelector('#modal-receta .btn-cancelar').addEventListener('click', cerrarModalReceta);
    document.querySelector('#modal-editar-receta .btn-cancelar').addEventListener('click', cerrarModalEditarReceta);
});
function agregarIngredienteEditar() {
    const listaIngredientes = document.getElementById('editar-lista-ingredientes');
    const ingredienteItem = document.createElement('div');
    ingredienteItem.classList.add('ingrediente-item');

    ingredienteItem.innerHTML = `
        <select name="ingredientes[]" class="ingrediente-select" required>
            <option value="">Seleccione un ingrediente</option>
            ${ingredientesDisponibles.map(ingrediente => `
                <option value="${ingrediente.id}">${ingrediente.nombre}</option>
            `).join('')}
        </select>
        <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" step="1" required>
        <button type="button" class="btn-remove-ingrediente" onclick="eliminarIngrediente(this)">Eliminar</button>
    `;

    listaIngredientes.appendChild(ingredienteItem);
}
