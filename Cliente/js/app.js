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

    // Actualizar los items del carrito
    itemsCarrito.innerHTML = carrito.map((item, index) => `
        <div class="cart-item">
            <div>
                <span class="item-name">${item.nombre}</span>
                <span class="item-price">$${item.precio}</span>
            </div>
            <button onclick="eliminarDelCarrito(${index})" class="remove-item">×</button>
        </div>
    `).join('');

    // Calcular el total
    const total = carrito.reduce((sum, item) => sum + parseFloat(item.precio), 0);
    totalCarrito.textContent = `$${total.toFixed(2)}`;
}

// Función para mostrar el carrito
function mostrarCarrito() {
    document.getElementById('carrito-flotante').classList.add('show');
}

// Función para eliminar un producto del carrito
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
    if (carrito.length === 0) {
        document.getElementById('carrito-flotante').classList.remove('show');
    }
}

// Función para confirmar el pedido
function confirmarPedido(event) {
    event.preventDefault();

    const nombreCliente = document.getElementById('nombre-cliente').value;
    const mesaSelect = document.getElementById('mesa-select').value;

    if (!nombreCliente || !mesaSelect) {
        alert('Por favor complete todos los campos');
        return;
    }

    const items = carrito.map(item => ({
        id: item.id,
        nombre: item.nombre,
        precio: item.precio,
        cantidad: 1
    }));

    if (items.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    const pedido = {
        nombreCliente: nombreCliente,
        items: items,
        id_mesa: parseInt(mesaSelect),
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
            document.getElementById('carrito-flotante').classList.remove('show');
            document.getElementById('nombre-cliente').value = '';
            document.getElementById('mesa-select').value = '';
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
    carritoFlotante.classList.toggle('show');
});

// Función para cargar productos con verificación de disponibilidad
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
                    card.className = 'producto-card';

                    card.innerHTML = `
                        <div class="card-hover">
                            <img src="${producto.imagen}" alt="${producto.nombre}" class="card-image">
                            <div class="card-body">
                                <div class="card-header">
                                    <h3>${producto.nombre}</h3>
                                    ${!producto.disponible ? 
                                        '<span class="not-available">No Disponible</span>' : ''
                                    }
                                </div>
                                <p>${producto.descripcion}</p>
                                <div class="card-footer">
                                    <span class="precio">$${producto.precio}</span>
                                    ${producto.disponible ? 
                                        `<button onclick="agregarAlCarrito(${JSON.stringify(producto).replace(/"/g, '&quot;')})" class="btn-agregar">
                                            Agregar al pedido
                                         </button>`
                                        :
                                        `<button class="btn-disabled" disabled>
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