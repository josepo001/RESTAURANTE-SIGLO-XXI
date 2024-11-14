let carrito = [];

// Función para agregar un producto al carrito
function agregarAlCarrito(producto) {
    carrito.push(producto);
    actualizarCarrito();
    mostrarCarrito();
}

// Función para actualizar el contenido del carrito en el DOM
function actualizarCarrito() {
    const contador = document.getElementById('carrito-contador');
    const itemsCarrito = document.getElementById('items-carrito');
    const totalCarrito = document.getElementById('total-carrito');

    // Actualizar el contador de elementos en el carrito
    contador.textContent = carrito.length;
    contador.classList.remove('hidden');

    // Generar HTML para cada producto en el carrito
    itemsCarrito.innerHTML = carrito.map((item, index) => `
        <div class="flex justify-between items-center mb-2">
            <div>
                <span class="font-medium">${item.nombre}</span>
                <span class="text-gray-600 ml-2">$${item.precio}</span>
            </div>
            <button onclick="eliminarDelCarrito(${index})" class="text-red-500 hover:text-red-700">×</button>
        </div>
    `).join('');

    // Calcular el total del carrito
    const total = carrito.reduce((sum, item) => sum + parseFloat(item.precio), 0);
    totalCarrito.textContent = `$${total.toFixed(2)}`;
}

// Función para mostrar el carrito
function mostrarCarrito() {
    document.getElementById('carrito-flotante').classList.remove('hidden');
}

// Función para eliminar un producto del carrito
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
    if (carrito.length === 0) {
        document.getElementById('carrito-flotante').classList.add('hidden');
        document.getElementById('carrito-contador').classList.add('hidden');
    }
}

// Función para confirmar el pedido
function confirmarPedido(event) {
    event.preventDefault();
    
    const nombreCliente = document.getElementById('nombre-cliente').value;
    const mesaSelect = document.getElementById('mesa-select');
    const id_mesa = mesaSelect.value;

    if (!nombreCliente || !id_mesa) {
        alert('Por favor complete todos los campos');
        return;
    }

    const items = carrito.map(item => ({
        id: item.id,
        nombre: item.nombre,
        precio: item.precio,
        cantidad: 1 // Ajustar según la cantidad que desees
    }));

    if (items.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    const pedido = {
        nombreCliente: nombreCliente,
        items: items,
        id_mesa: parseInt(id_mesa),
        total: parseFloat(document.getElementById('total-carrito').textContent.slice(1))
    };

    fetch('api/procesar_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(pedido)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(`¡Pedido #${data.idPedido} creado exitosamente!`);
            carrito = [];
            actualizarCarrito();
            document.getElementById('carrito-flotante').classList.add('hidden');
            document.getElementById('nombre-cliente').value = '';
            document.getElementById('mesa-select').value = '';
            document.getElementById('carrito-contador').classList.add('hidden');
            location.reload();
        } else {
            alert(`Error al crear el pedido: ${data.error}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar el pedido. Por favor, inténtelo de nuevo.');
    });
}

// Evento para mostrar el carrito cuando se hace clic en el ícono
document.getElementById('carrito-icon').addEventListener('click', () => {
    const carritoFlotante = document.getElementById('carrito-flotante');
    carritoFlotante.classList.toggle('hidden');
});


// Cargar productos con verificación de disponibilidad
function cargarProductos(categoria = 'todos') {
    fetch('api/productos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const productos = data.productos.filter(p => 
                    categoria === 'todos' || p.categoria === categoria
                );
                
                const contenedor = document.getElementById('productos-container');
                contenedor.innerHTML = '';

                productos.forEach(producto => {
                    const card = document.createElement('div');
                    card.className = 'producto-card mb-6';
                    
                    card.innerHTML = `
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <img src="${producto.imagen}" alt="${producto.nombre}" 
                                 class="w-full h-48 object-cover">
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-xl font-bold">${producto.nombre}</h3>
                                    ${!producto.disponible ? 
                                        '<span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded-full">No Disponible</span>' 
                                        : ''
                                    }
                                </div>
                                <p class="text-gray-600 mt-2">${producto.descripcion}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-xl font-bold">$${producto.precio}</span>
                                    ${producto.disponible ? 
                                        `<button onclick="agregarAlCarrito(${JSON.stringify(producto).replace(/"/g, '&quot;')})" 
                                                 class="bg-[#8B4513] text-white px-4 py-2 rounded hover:bg-amber-700 transition-colors">
                                            Agregar al pedido
                                         </button>`
                                        :
                                        `<button disabled 
                                                 class="bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed">
                                            No disponible
                                         </button>`
                                    }
                                </div>
                            </div>
                        </div>
                    `;
                    
                    contenedor.appendChild(card);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los productos');
        });
}

function agregarAlCarrito(producto) {
    // Verificar disponibilidad antes de agregar al carrito
    if (producto.disponible === false) {
        alert('Este producto no está disponible actualmente');
        return;
    }
    
    carrito.push(producto);
    actualizarCarrito();
    mostrarCarrito();
}