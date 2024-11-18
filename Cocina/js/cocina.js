// Inicializar contenedores para cada estado
const contenedoresEstados = {
    pendiente: document.getElementById('pedidos-pendientes'),
    "en preparación": document.getElementById('pedidos-preparacion'),
    completado: document.getElementById('pedidos-listos'),
    entregado: document.getElementById('pedidos-listos-para-pagar')
};

// Función para cargar todos los pedidos desde la API
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

// Función para actualizar el tablero
function actualizarTablero(pedidos) {
    for (const contenedor in contenedoresEstados) {
        contenedoresEstados[contenedor].innerHTML = '';
    }
    pedidos.forEach(pedido => {
        const pedidoElement = crearElementoPedido(pedido);
        contenedoresEstados[pedido.estado]?.appendChild(pedidoElement);
    });
}

// Crear el elemento visual de un pedido
function crearElementoPedido(pedido) {
    const div = document.createElement('div');
    div.classList.add('card-pedido'); // Añade la clase CSS
    div.onclick = () => mostrarDetallesPedido(pedido);

    const estadoClass = getEstadoClass(pedido.estado);

    div.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <h4 class="font-bold">Pedido #${pedido.id}</h4>
            <span class="${estadoClass}">${pedido.tiempo_estimado || 0} min</span>
        </div>
        <p>Mesa ${pedido.id_mesa}</p>
        <p><strong>Total: $${pedido.total || 0}</strong></p>
        <div>
            ${pedido.items.map(item => `<div>${item.cantidad}x ${item.nombre}</div>`).join('')}
        </div>
        <div>
            ${crearBotonesAccion(pedido)}
        </div>
    `;
    return div;
}

// Obtener la clase de estilo según el estado
function getEstadoClass(estado) {
    switch (estado) {
        case 'pendiente': return 'text-rojo';
        case 'en preparación': return 'text-amarillo';
        case 'completado': return 'text-verde';
        case 'entregado': return 'text-morado';
        default: return '';
    }
}

// Crear botones de acción
function crearBotonesAccion(pedido) {
    switch (pedido.estado) {
        case 'pendiente':
            return `<button onclick="actualizarEstadoPedido(${pedido.id}, 'en preparación')" class="btn btn-amarillo">Iniciar Preparación</button>`;
        case 'en preparación':
            return `<button onclick="actualizarEstadoPedido(${pedido.id}, 'completado')" class="btn btn-verde">Marcar Listo</button>`;
        case 'completado':
            return `<button onclick="actualizarEstadoPedido(${pedido.id}, 'entregado')" class="btn btn-azul">Entregado</button>`;
        case 'entregado':
            return `<button onclick="registrarPago(${pedido.id}, ${pedido.id_mesa}, ${pedido.total})" class="btn btn-morado">Pagar</button>`;
        default:
            return '';
    }
}

// Actualizar el estado de un pedido
function actualizarEstadoPedido(idPedido, nuevoEstado) {
    fetch('../Cocina/api/actualizar_estado_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pedido: idPedido, estado: nuevoEstado })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarPedidos();
            } else {
                alert('Error al actualizar el estado');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Registrar el pago
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
                cargarPedidos();
            } else {
                alert('Error al registrar el pago');
            }
        })
        .catch(error => console.error('Error:', error));
}

// Cargar pedidos al iniciar y cada 10 segundos
document.addEventListener('DOMContentLoaded', () => {
    cargarPedidos();
    setInterval(cargarPedidos, 1000);
});
