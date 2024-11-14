// Inicializar contenedores para cada estado
const contenedoresEstados = {
    pendiente: document.getElementById('pedidos-pendientes'),
    "en preparación": document.getElementById('pedidos-preparacion'),
    completado: document.getElementById('pedidos-listos'),
    entregado: document.getElementById('pedidos-listos-para-pagar')
};

// Función para cargar todos los pedidos desde la API y actualizar el tablero completo
function cargarPedidos() {
    fetch('../Cocina/api/obtener_pedidos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTablero(data.pedidos);
            }
        })
        .catch(error => console.error('Error al cargar los pedidos:', error));
}

// Función para actualizar el tablero completo
function actualizarTablero(pedidos) {
    // Limpiar todos los contenedores de estado
    for (const contenedor in contenedoresEstados) {
        contenedoresEstados[contenedor].innerHTML = '';
    }

    // Llenar cada contenedor con los pedidos correspondientes
    pedidos.forEach(pedido => {
        const pedidoElement = crearElementoPedido(pedido);
        contenedoresEstados[pedido.estado]?.appendChild(pedidoElement);
    });
}

// Función para crear el elemento visual de un pedido
function crearElementoPedido(pedido) {
    const div = document.createElement('div');
    div.className = 'bg-gray-50 p-4 rounded-lg border hover:shadow-md transition-shadow cursor-pointer';
    div.onclick = () => mostrarDetallesPedido(pedido);

    let estadoClass = getEstadoClass(pedido.estado);

    div.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <h4 class="font-bold">Pedido #${pedido.id}</h4>
            <span class="text-sm ${estadoClass}">${pedido.tiempo_estimado || 0} min</span>
        </div>
        <p class="text-sm text-gray-600 mb-2">Mesa ${pedido.id_mesa}</p>
        <p class="text-sm text-gray-600 mb-2 font-semibold">Total: $${pedido.total || 0}</p>
        <div class="space-y-1">
            ${pedido.items.map(item => `
                <div class="flex justify-between text-sm">
                    <span>${item.cantidad}x ${item.nombre}</span>
                </div>
            `).join('')}
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            ${crearBotonesAccion(pedido)}
        </div>
    `;

    return div;
}

// Función para obtener la clase de estilo según el estado
function getEstadoClass(estado) {
    switch (estado) {
        case 'pendiente': return 'text-red-600';
        case 'en preparación': return 'text-yellow-600';
        case 'completado': return 'text-green-600';
        case 'entregado': return 'text-purple-600';
        default: return 'text-gray-600';
    }
}

// Función para crear los botones de acción según el estado
function crearBotonesAccion(pedido) {
    switch (pedido.estado) {
        case 'pendiente':
            return `<button onclick="actualizarEstadoPedido(${pedido.id}, 'en preparación')" 
                    class="px-3 py-1 bg-yellow-500 text-white rounded">Iniciar Preparación</button>`;
        case 'en preparación':
            return `<button onclick="actualizarEstadoPedido(${pedido.id}, 'completado')" 
                    class="px-3 py-1 bg-green-500 text-white rounded">Marcar Listo</button>`;
        case 'completado':
            return `<button onclick="actualizarEstadoPedido(${pedido.id}, 'entregado')" 
                    class="px-3 py-1 bg-blue-500 text-white rounded">Entregado</button>`;
        case 'entregado':
            return `<button onclick="registrarPago(${pedido.id}, ${pedido.id_mesa}, ${pedido.total})" 
                    class="px-3 py-1 bg-purple-500 text-white rounded">Pagar</button>`;
        default:
            return '';
    }
}

// Función para actualizar el estado de un pedido y refrescar el tablero completo
function actualizarEstadoPedido(idPedido, nuevoEstado) {
    fetch('../Cocina/api/actualizar_estado_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pedido: idPedido, estado: nuevoEstado })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarPedidos(); // Recargar pedidos justo después de cambiar el estado
        } else {
            alert('Error al actualizar el estado del pedido');
        }
    })
    .catch(error => console.error('Error:', error));
}


// Función para registrar el pago y refrescar el tablero completo
function registrarPago(idPedido, idMesa, total) {
    fetch('../Cocina/api/registrar_pago.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pedido: idPedido, id_mesa: idMesa, total: total })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pago registrado exitosamente');
            cargarPedidos(); // Refrescar el tablero después de registrar el pago
        } else {
            alert('Error al registrar el pago: ' + data.error);
        }
    })
    .catch(error => console.error('Error al registrar el pago:', error));
}

// Cargar pedidos inicialmente y actualizar cada 10 segundos para asegurar visibilidad
document.addEventListener('DOMContentLoaded', () => {
    cargarPedidos();
    // Refresca la lista de pedidos cada 1 segundos
setInterval(cargarPedidos, 1000);
// Refresca la lista de pedidos cada 10 segundos
});
