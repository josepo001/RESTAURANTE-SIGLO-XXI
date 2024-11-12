// Variables globales
let productosActuales = [];
let recetasActuales = [];
let pedidosActuales = [];
let ingredientesDisponibles = [];

// Funciones de inicialización
document.addEventListener('DOMContentLoaded', () => {
    cargarResumen();
    cargarProductos();
});

// Funciones para cambiar entre secciones
function mostrarSeccion(seccion) {
    // Ocultar todas las secciones
    document.querySelectorAll('[id^="seccion-"]').forEach(el => {
        el.classList.add('hidden');
    });

    // Mostrar la sección seleccionada
    const seccionActual = document.getElementById(`seccion-${seccion}`);
    if (seccionActual) {
        seccionActual.classList.remove('hidden');
    }

    // Cargar los datos correspondientes
    switch(seccion) {
        case 'productos':
            cargarProductos();
            break;
        case 'recetas':
            cargarRecetas();
            break;
        case 'pedidos':
            cargarPedidos();
            break;
    }
}

// Función para mostrar modal de producto
function mostrarFormularioProducto(id = null) {
    const modal = document.getElementById('modal-producto');
    const form = document.getElementById('form-producto');

    if (id) {
        const producto = productosActuales.find(p => p.id === id);
        if (producto) {
            form.nombre.value = producto.nombre;
            form.stock.value = producto.stock;
            form.stock_minimo.value = producto.stock_minimo;
            form.unidad.value = producto.unidad;
            form.dataset.id = id;
        }
    } else {
        form.reset();
        delete form.dataset.id;
    }

    modal.classList.remove('hidden');
}

// Función para editar producto
function editarProducto(id) {
    mostrarFormularioProducto(id);
}

// Función ajustarStock modificada para debugging adicional
function ajustarStock(id) {
    console.log('Iniciando ajuste de stock para ID:', id); // Debug
    const producto = productosActuales.find(p => p.id === id);
    if (!producto) {
        console.error('Producto no encontrado para ID:', id);
        return;
    }

    console.log('Producto encontrado:', producto); // Debug

    const nuevoStock = prompt(`Ajustar stock para ${producto.nombre}\nStock actual: ${producto.stock}\nIngrese nuevo valor:`, producto.stock);
    
    if (nuevoStock === null || nuevoStock.trim() === '') {
        console.log('Operación cancelada por el usuario');
        return;
    }

    const stockNumerico = parseInt(nuevoStock);
    if (isNaN(stockNumerico) || stockNumerico < 0) {
        alert('Por favor ingrese un número válido mayor o igual a 0');
        return;
    }

    console.log('Preparando datos para enviar:', { id, stock: stockNumerico }); // Debug

    const formData = new FormData();
    formData.append('id', id);
    formData.append('stock', stockNumerico);

    fetch('api/ajustar_stock.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Respuesta recibida:', response.status); // Debug
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data); // Debug
        if (data.success) {
            alert(`Stock actualizado correctamente a ${stockNumerico}`);
            cargarProductos();
            cargarResumen();
        } else {
            throw new Error(data.message || 'Error al actualizar stock');
        }
    })
    .catch(error => {
        console.error('Error en la operación:', error);
        alert('Error al ajustar stock: ' + error.message);
    });
}

// Función para cargar productos
function cargarProductos() {
    fetch('api/obtener_productos.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (data.success) {
                productosActuales = data.productos;
                actualizarTablaProductos();
            } else {
                console.error('Error al cargar productos:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
        });
}

// Función para actualizar la tabla de productos
function actualizarTablaProductos() {
    const tbody = document.getElementById('lista-productos');
    tbody.innerHTML = '';

    productosActuales.forEach(producto => {
        const tr = document.createElement('tr');
        const stockClass = producto.stock <= producto.stock_minimo ? 'text-red-600' : '';
        
        // Crear botones como elementos separados para mejor control
        const btnEditar = document.createElement('button');
        btnEditar.className = 'text-blue-600 hover:text-blue-800 mr-3';
        btnEditar.textContent = 'Editar';
        btnEditar.onclick = () => editarProducto(producto.id);

        const btnAjustar = document.createElement('button');
        btnAjustar.className = 'text-green-600 hover:text-green-800';
        btnAjustar.textContent = 'Ajustar Stock';
        btnAjustar.onclick = () => ajustarStock(producto.id);

        // Crear contenido de la fila
        tr.innerHTML = `
            <td class="px-6 py-4">${producto.nombre}</td>
            <td class="px-6 py-4 ${stockClass}">${producto.stock}</td>
            <td class="px-6 py-4">${producto.stock_minimo}</td>
            <td class="px-6 py-4">${producto.unidad}</td>
            <td class="px-6 py-4"></td>
        `;

        // Agregar botones a la última celda
        const ultimaCelda = tr.querySelector('td:last-child');
        ultimaCelda.appendChild(btnEditar);
        ultimaCelda.appendChild(btnAjustar);

        tbody.appendChild(tr);
    });
}

// Función para cerrar modal
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Función para guardar producto
function guardarProducto(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData();
    
    formData.append('nombre', form.nombre.value);
    formData.append('stock', form.stock.value);
    formData.append('stock_minimo', form.stock_minimo.value);
    formData.append('unidad', form.unidad.value);
    
    if (form.dataset.id) {
        formData.append('id', form.dataset.id);
    }

    console.log('Enviando datos:', Object.fromEntries(formData));

    fetch('api/guardar_producto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            cerrarModal('modal-producto');
            cargarProductos();
            cargarResumen();
        } else {
            alert('Error al guardar el producto: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud: ' + error.message);
    });
}

// Función para cargar el resumen
function cargarResumen() {
    fetch('api/obtener_resumen.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-productos').textContent = data.totalProductos || 0;
                document.getElementById('productos-bajos').textContent = data.productosBajos || 0;
                document.getElementById('pedidos-pendientes').textContent = data.pedidosPendientes || 0;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Función para cargar recetas
function cargarRecetas() {
    // Primero cargar los ingredientes
    fetch('api/obtener_productos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ingredientesDisponibles = data.productos;
                // Ahora cargar las recetas
                return fetch('api/obtener_recetas.php');
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                recetasActuales = data.recetas;
                actualizarTablaRecetas();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar las recetas');
        });
}

function verificarStockReceta(ingredientes) {
    return ingredientes.every(ing => {
        // Buscar el ingrediente en la lista de productos disponibles
        const ingredienteEnStock = productosActuales.find(p => 
            p.nombre.toLowerCase() === ing.nombre.toLowerCase()
        );
        
        if (!ingredienteEnStock) {
            console.log(`Ingrediente no encontrado: ${ing.nombre}`);
            return false;
        }

        // Convertir las cantidades a números para la comparación
        const cantidadNecesaria = parseFloat(ing.cantidad);
        const stockDisponible = parseFloat(ingredienteEnStock.stock);
        
        console.log(`Verificando ${ing.nombre}: necesita ${cantidadNecesaria}, hay ${stockDisponible}`);
        
        return stockDisponible >= cantidadNecesaria;
    });
}

// Y actualizar la función que muestra el estado en la tabla
function actualizarTablaRecetas() {
    const tbody = document.getElementById('lista-recetas');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    recetasActuales.forEach(receta => {
        const tr = document.createElement('tr');
        const stockSuficiente = verificarStockReceta(receta.ingredientes);
        
        tr.innerHTML = `
            <td class="px-6 py-4">${receta.nombre_producto || 'Producto no encontrado'}</td>
            <td class="px-6 py-4">
                ${receta.ingredientes.map(ing => 
                    `${ing.cantidad} ${ing.unidad || 'unidad'} de ${ing.nombre}`
                ).join('<br>')}
            </td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded ${
                    stockSuficiente 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'
                }">
                    ${stockSuficiente ? 'Stock Disponible' : 'Stock Insuficiente'}
                </span>
            </td>
            <td class="px-6 py-4">
                <button onclick="editarReceta(${receta.id_producto})" 
                        class="text-blue-600 hover:text-blue-800 mr-3">
                    Editar
                </button>
                <button onclick="eliminarReceta(${receta.id_producto})"
                        class="text-red-600 hover:text-red-800">
                    Eliminar
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}



// Función para cargar ingredientes
function cargarIngredientes() {
    return fetch('api/obtener_productos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ingredientesDisponibles = data.productos;
                return data.productos;
            }
            throw new Error('Error al cargar ingredientes');
        });
}

// Función para mostrar el formulario de receta
function mostrarFormularioReceta(id = null) {
    const modal = document.getElementById('modal-receta');
    const form = document.getElementById('form-receta');
    
    // Limpiar la lista de ingredientes actual
    const listaIngredientes = document.getElementById('lista-ingredientes-receta');
    listaIngredientes.innerHTML = '';
    
    // Cargar productos del menú
    fetch('api/obtener_productos_menu.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('producto-receta');
                select.innerHTML = '<option value="">Seleccione un producto</option>';
                data.productos.forEach(producto => {
                    const selected = id && id === producto.id ? 'selected' : '';
                    select.innerHTML += `
                        <option value="${producto.id}" ${selected}>
                            ${producto.nombre} (${producto.categoria})
                        </option>
                    `;
                });

                // Si es edición, cargar los ingredientes existentes
                if (id) {
                    const receta = recetasActuales.find(r => r.id_producto === id);
                    if (receta) {
                        receta.ingredientes.forEach(ingrediente => {
                            agregarIngredienteAReceta(ingrediente);
                        });
                    }
                }

                modal.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del formulario');
        });
}

// Función modificada para agregar ingrediente
function agregarIngredienteAReceta(ingredienteExistente = null) {
    const listaIngredientes = document.getElementById('lista-ingredientes-receta');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-4 p-3 bg-gray-50 rounded';
    
    div.innerHTML = `
        <select name="ingredientes[]" required class="flex-1 rounded-md border border-gray-300 px-3 py-2">
            <option value="">Seleccione ingrediente</option>
            ${ingredientesDisponibles.map(ing => `
                <option value="${ing.id}" 
                    ${ingredienteExistente && ing.id === ingredienteExistente.id ? 'selected' : ''}>
                    ${ing.nombre} (${ing.unidad})
                </option>
            `).join('')}
        </select>
        <input type="number" 
               name="cantidades[]" 
               required 
               min="0.01" 
               step="0.01"
               placeholder="Cantidad"
               value="${ingredienteExistente ? ingredienteExistente.cantidad : ''}"
               class="w-32 rounded-md border border-gray-300 px-3 py-2">
        <button type="button" 
                onclick="this.parentElement.remove()"
                class="text-red-600 hover:text-red-800">
            Eliminar
        </button>
    `;
    
    listaIngredientes.appendChild(div);
}


// Función modificada para cargar recetas
function cargarRecetas() {
    // Primero cargar los ingredientes disponibles
    cargarIngredientes()
        .then(() => {
            // Luego cargar las recetas
            return fetch('api/obtener_recetas.php');
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                recetasActuales = data.recetas;
                actualizarTablaRecetas();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar las recetas');
        });
}

// Modificar el evento DOMContentLoaded para incluir la carga inicial de ingredientes
document.addEventListener('DOMContentLoaded', () => {
    cargarIngredientes().then(() => {
        cargarResumen();
        cargarProductos();
    });
});

// Botón de agregar ingrediente
document.addEventListener('click', (e) => {
    if (e.target.matches('#agregar-ingrediente') || 
        e.target.closest('#agregar-ingrediente')) {
        e.preventDefault();
        agregarIngredienteAReceta();
    }
});

// Función para guardar receta
function guardarReceta(event) {
    event.preventDefault();
    
    const id_producto = document.getElementById('producto-receta').value;
    const ingredientesSelects = document.getElementsByName('ingredientes[]');
    const cantidadesInputs = document.getElementsByName('cantidades[]');
    
    if (!id_producto) {
        alert('Por favor seleccione un producto');
        return;
    }

    const ingredientes = [];
    for (let i = 0; i < ingredientesSelects.length; i++) {
        const id_ingrediente = ingredientesSelects[i].value;
        const cantidad = cantidadesInputs[i].value;
        
        if (!id_ingrediente || !cantidad) {
            alert('Por favor complete todos los campos de ingredientes');
            return;
        }

        ingredientes.push({
            id: parseInt(id_ingrediente),
            cantidad: parseFloat(cantidad)
        });
    }

    if (ingredientes.length === 0) {
        alert('Por favor agregue al menos un ingrediente');
        return;
    }

    const recetaData = {
        id_producto: parseInt(id_producto),
        ingredientes: ingredientes
    };

    console.log('Enviando receta:', recetaData);

    fetch('api/guardar_receta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(recetaData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Receta guardada correctamente');
            cerrarModal('modal-receta');
            cargarRecetas();
        } else {
            throw new Error(data.message || 'Error al guardar la receta');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la receta: ' + error.message);
    });
}

// Función helper para agregar un ingrediente al formulario
function agregarIngredienteAReceta() {
    const listaIngredientes = document.getElementById('lista-ingredientes-receta');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-4 p-3 bg-gray-50 rounded';
    
    div.innerHTML = `
        <select name="ingredientes[]" required 
                class="flex-1 rounded-md border border-gray-300 px-3 py-2">
            <option value="">Seleccione ingrediente</option>
            ${ingredientesDisponibles.map(ing => 
                `<option value="${ing.id}">${ing.nombre} (${ing.unidad})</option>`
            ).join('')}
        </select>
        <input type="number" 
               name="cantidades[]" 
               required 
               min="0.01" 
               step="0.01"
               class="w-32 rounded-md border border-gray-300 px-3 py-2"
               placeholder="Cantidad">
        <button type="button" 
                onclick="this.parentElement.remove()"
                class="text-red-600 hover:text-red-800">
            Eliminar
        </button>
    `;
    
    listaIngredientes.appendChild(div);
}

function eliminarReceta(id) {
    if (!confirm('¿Está seguro de eliminar esta receta?')) return;

    fetch('api/eliminar_receta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarRecetas();
            alert('Receta eliminada correctamente');
        } else {
            alert('Error al eliminar la receta: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la receta');
    });
}



function mostrarFormularioPedido(id = null) {
    const modal = document.getElementById('modal-pedido');
    const form = document.getElementById('form-pedido');
    
    // Limpiar el formulario
    form.reset();
    document.getElementById('lista-productos-pedido').innerHTML = '';

    // Cargar proveedores primero
    cargarProveedores().then(() => {
        if (id) {
            // Modo edición
            fetch(`api/obtener_pedido.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Datos del pedido:', data); // Para depuración
                    if (data.success) {
                        // Establecer los valores del formulario
                        form.dataset.id = id; // Agregar el ID del pedido al formulario
                        form.querySelector('#proveedor').value = data.pedido.id_proveedor;
                        form.querySelector('#estado').value = data.pedido.estado;
                        
                        // Cargar los productos del pedido
                        if (data.pedido.productos && data.pedido.productos.length > 0) {
                            data.pedido.productos.forEach(producto => {
                                agregarProductoPedido(producto);
                            });
                        } else {
                            agregarProductoPedido();
                        }
                        
                        modal.classList.remove('hidden');
                    } else {
                        alert('Error al cargar el pedido: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al cargar pedido:', error);
                    alert('Error al cargar el pedido');
                });
        } else {
            // Modo nuevo pedido
            delete form.dataset.id;
            agregarProductoPedido();
            modal.classList.remove('hidden');
        }
    });
}

function actualizarProductosProveedor(idProveedor) {
    const productoSelect = document.querySelector('select[name="productos[]"]');
    if (!productoSelect) return;

    fetch(`api/obtener_productos_proveedor.php?id_proveedor=${idProveedor}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
                data.productos.forEach(producto => {
                    productoSelect.innerHTML += `
                        <option value="${producto.id}" data-precio="${producto.precio_unitario}">
                            ${producto.nombre} - $${producto.precio_unitario}
                        </option>
                    `;
                });
            }
        });
}

function actualizarPrecioProducto(select) {
    const option = select.options[select.selectedIndex];
    const precioInput = select.closest('div').querySelector('input[name="precios[]"]');
    if (option && option.dataset.precio) {
        precioInput.value = option.dataset.precio;
    }
}

// Función para guardar pedido
function guardarPedido(event) {
    event.preventDefault();
    const form = event.target;
    const productos = Array.from(document.querySelectorAll('#lista-productos-pedido > div')).map(div => ({
        id: div.querySelector('select[name="productos[]"]').value,
        cantidad: div.querySelector('input[name="cantidades[]"]').value,
        precio: div.querySelector('input[name="precios[]"]').value
    }));

    const pedidoData = {
        id_proveedor: form.proveedor.value,
        estado: form.estado.value,
        productos: productos
    };

    // Si es edición, agregar el ID del pedido
    if (form.dataset.id) {
        pedidoData.id = form.dataset.id;
    }

    fetch('api/guardar_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(pedidoData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cerrarModal('modal-pedido');
            cargarPedidos();
            cargarResumen();
            alert('Pedido guardado correctamente');
        } else {
            throw new Error(data.message || 'Error al guardar el pedido');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar el pedido: ' + error.message);
    });
}

// Funciones auxiliares
function getEstadoClass(estado) {
    const clases = {
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'en tránsito': 'bg-blue-100 text-blue-800',
        'recibido': 'bg-green-100 text-green-800',
        'cancelado': 'bg-red-100 text-red-800'
    };
    return clases[estado] || 'bg-gray-100 text-gray-800';
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatearNumero(numero) {
    return Number(numero).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function capitalizarPrimeraLetra(texto) {
    return texto.charAt(0).toUpperCase() + texto.slice(1);
}

// Agregar al evento DOMContentLoaded existente
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('seccion-pedidos')) {
        cargarPedidos();
    }
});

// Función para cargar pedidos
function cargarPedidos() {
    fetch('api/obtener_pedidos.php')
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta pedidos:', data); // Para depuración
            if (data.success) {
                pedidosActuales = data.pedidos;
                actualizarTablaPedidos();
            } else {
                console.error('Error al cargar pedidos:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


function actualizarTablaPedidos() {
    const tbody = document.getElementById('lista-pedidos');
    if (!tbody) return;
    
    tbody.innerHTML = '';

    pedidosActuales.forEach(pedido => {
        const tr = document.createElement('tr');
        
        let productosHTML = pedido.productos.map(prod => 
            `${prod.cantidad} × ${prod.nombre} ($${prod.precio_unitario})`
        ).join('<br>');

        tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="font-medium">${pedido.proveedor_nombre}</div>
                <div class="text-sm text-gray-500">
                    ${pedido.contacto} - ${pedido.telefono}
                </div>
                <div class="mt-2 text-sm">
                    <strong>Productos:</strong><br>
                    ${productosHTML}
                </div>
            </td>
            <td class="px-6 py-4">${formatearFecha(pedido.fecha_pedido)}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-sm ${getEstadoClass(pedido.estado)}">
                    ${capitalizarPrimeraLetra(pedido.estado)}
                </span>
            </td>
            <td class="px-6 py-4">$${formatearNumero(pedido.total)}</td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <button onclick="editarPedido(${pedido.id})" 
                            class="text-blue-600 hover:text-blue-800">
                        Editar
                    </button>
                    ${pedido.estado !== 'recibido' && pedido.estado !== 'cancelado' ? `
                        <button onclick="eliminarPedido(${pedido.id})"
                                class="text-red-600 hover:text-red-800">
                            Eliminar
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
}

// Función para cargar proveedores
function cargarProveedores() {
    return fetch('api/obtener_proveedores.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('proveedor');
                if (select) {
                    select.innerHTML = '<option value="">Seleccione un proveedor</option>';
                    data.proveedores.forEach(proveedor => {
                        select.innerHTML += `
                            <option value="${proveedor.id}">
                                ${proveedor.nombre}
                            </option>
                        `;
                    });
                }
            }
            return data;
        })
        .catch(error => {
            console.error('Error al cargar proveedores:', error);
            return { success: false, error };
        });
}

// Función para agregar producto al pedido
function agregarProductoPedido(itemExistente = null) {
    const div = document.createElement('div');
    div.className = 'flex items-center gap-4 p-3 bg-gray-50 rounded';
    
    div.innerHTML = `
        <select name="productos[]" required 
                class="flex-1 rounded-md border-gray-300 px-3 py-2"
                onchange="actualizarUnidadProducto(this)">
            <option value="">Seleccione un producto</option>
            ${productosActuales.map(prod => `
                <option value="${prod.id}" 
                    ${itemExistente && itemExistente.id_producto == prod.id ? 'selected' : ''}
                    data-unidad="${prod.unidad}">
                    ${prod.nombre} (${prod.unidad})
                </option>
            `).join('')}
        </select>
        <input type="number" 
               name="cantidades[]" 
               placeholder="Cantidad"
               value="${itemExistente ? itemExistente.cantidad : ''}"
               class="w-24 rounded-md border-gray-300 px-3 py-2"
               required min="1">
        <span class="unidad-medida text-sm text-gray-500 w-20">
            ${itemExistente ? itemExistente.unidad || '' : ''}
        </span>
        <input type="number" 
               name="precios[]" 
               placeholder="Precio unitario"
               value="${itemExistente ? itemExistente.precio : ''}"
               class="w-32 rounded-md border-gray-300 px-3 py-2"
               required min="0" step="0.01">
        <button type="button" 
                onclick="this.parentElement.remove()"
                class="text-red-600 hover:text-red-800">
            Eliminar
        </button>
    `;
    
    document.getElementById('lista-productos-pedido').appendChild(div);
}

// Función para actualizar la unidad del producto seleccionado
function actualizarUnidadProducto(select) {
    const unidadSpan = select.parentElement.querySelector('.unidad-medida');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        unidadSpan.textContent = selectedOption.dataset.unidad;
    } else {
        unidadSpan.textContent = '';
    }
}


// Función para eliminar pedido
function eliminarPedido(id) {
    if (!confirm('¿Está seguro de eliminar este pedido?')) return;

    fetch('api/eliminar_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarPedidos();
            alert('Pedido eliminado correctamente');
        } else {
            alert('Error al eliminar el pedido: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el pedido');
    });
}


// Función para editar pedido
function editarPedido(id) {
    console.log('Editando pedido:', id); // Para depuración
    mostrarFormularioPedido(id);
}

// Modificar la función mostrarFormularioPedido
function mostrarFormularioPedido(id = null) {
    const modal = document.getElementById('modal-pedido');
    const form = document.getElementById('form-pedido');
    
    // Limpiar el formulario
    form.reset();
    document.getElementById('lista-productos-pedido').innerHTML = '';

    // Cargar proveedores primero
    cargarProveedores().then(() => {
        if (id) {
            // Si es edición, cargar los datos del pedido
            fetch(`api/obtener_pedido.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('proveedor').value = data.pedido.id_proveedor;
                        document.getElementById('estado').value = data.pedido.estado;
                        
                        // Cargar los productos del pedido
                        if (data.pedido.productos && data.pedido.productos.length > 0) {
                            data.pedido.productos.forEach(producto => {
                                agregarProductoPedido(producto);
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al cargar pedido:', error);
                });
        } else {
            // Si es nuevo pedido, agregar una línea vacía de producto
            agregarProductoPedido();
        }
        
        modal.classList.remove('hidden');
    });
}

// Función para agregar producto al pedido
function agregarProductoPedido(productoExistente = null) {
    const div = document.createElement('div');
    div.className = 'flex items-center gap-4 p-3 bg-gray-50 rounded';
    
    div.innerHTML = `
        <select name="productos[]" required 
                class="flex-1 rounded-md border-gray-300 px-3 py-2"
                onchange="actualizarPrecioProducto(this)">
            <option value="">Seleccione un producto</option>
            ${productosActuales.map(prod => `
                <option value="${prod.id}" 
                    ${productoExistente && productoExistente.id_producto == prod.id ? 'selected' : ''}
                    data-unidad="${prod.unidad}">
                    ${prod.nombre} (${prod.unidad})
                </option>
            `).join('')}
        </select>
        <input type="number" 
               name="cantidades[]" 
               placeholder="Cantidad"
               value="${productoExistente ? productoExistente.cantidad : ''}"
               class="w-24 rounded-md border-gray-300 px-3 py-2"
               required min="1">
        <input type="number" 
               name="precios[]" 
               placeholder="Precio unitario"
               value="${productoExistente ? productoExistente.precio : ''}"
               class="w-32 rounded-md border-gray-300 px-3 py-2"
               required min="0" step="0.01">
        <button type="button" 
                onclick="this.parentElement.remove()"
                class="text-red-600 hover:text-red-800">
            Eliminar
        </button>
    `;
    
    document.getElementById('lista-productos-pedido').appendChild(div);
}

// Función para actualizar el precio al seleccionar un producto
function actualizarPrecioProducto(select) {
    const option = select.options[select.selectedIndex];
    const div = select.closest('div');
    const precioInput = div.querySelector('input[name="precios[]"]');
    
    if (option.value) {
        const producto = productosActuales.find(p => p.id == option.value);
        if (producto && producto.precio_sugerido) {
            precioInput.value = producto.precio_sugerido;
        }
    }
}
// Actualizaciones automáticas
setInterval(cargarResumen, 30000); // Actualizar cada 30 segundos