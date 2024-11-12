let pedidosActuales = {
    pendientes: [],
    enPreparacion: [],
    listos: []
};

// Función para cargar los pedidos
function cargarPedidos() {
    fetch('../Cocina/api/obtener_pedidos.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTablero(data.pedidos);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Función para actualizar el tablero
function actualizarTablero(pedidos) {
    const pendientesDiv = document.getElementById('pedidos-pendientes');
    const preparacionDiv = document.getElementById('pedidos-preparacion');
    const listosDiv = document.getElementById('pedidos-listos');

    // Limpiar contenedores
    pendientesDiv.innerHTML = '';
    preparacionDiv.innerHTML = '';
    listosDiv.innerHTML = '';

    // Ordenar pedidos por tiempo y prioridad
    pedidos.forEach(pedido => {
        const pedidoElement = crearElementoPedido(pedido);
        
        switch (pedido.estado) {
            case 'pendiente':
                pendientesDiv.appendChild(pedidoElement);
                break;
            case 'en preparación':
                preparacionDiv.appendChild(pedidoElement);
                break;
            case 'completado':
                listosDiv.appendChild(pedidoElement);
                break;
        }
    });
}

// Función para crear el elemento visual de un pedido
function crearElementoPedido(pedido) {
    const div = document.createElement('div');
    div.className = 'bg-gray-50 p-4 rounded-lg border hover:shadow-md transition-shadow cursor-pointer';
    div.onclick = () => mostrarDetallesPedido(pedido);
    
    let estadoClass = pedido.estado === 'pendiente' ? 'text-red-600' : 
                      pedido.estado === 'en preparación' ? 'text-yellow-600' : 
                      'text-green-600';

    div.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <h4 class="font-bold">Pedido #${pedido.id}</h4>
            <span class="text-sm ${estadoClass}">${pedido.tiempo_estimado} min</span>
        </div>
        <p class="text-sm text-gray-600 mb-2">Mesa ${pedido.id_mesa}</p>
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

// Función para mostrar detalles del pedido
function mostrarDetallesPedido(pedido) {
    const detalleDiv = document.getElementById('detalle-pedido');
    
    // Calcular el tiempo transcurrido
    const fechaPedido = new Date(pedido.fecha_pedido);
    const ahora = new Date();
    const tiempoTranscurrido = Math.floor((ahora - fechaPedido) / 1000 / 60); // en minutos

    detalleDiv.innerHTML = `
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="grid grid-cols-2 gap-6">
                <!-- Información básica -->
                <div>
                    <h4 class="font-bold text-lg mb-4">Pedido #${pedido.id}</h4>
                    <div class="space-y-2">
                        <p class="flex justify-between">
                            <span class="text-gray-600">Mesa:</span>
                            <span class="font-medium">${pedido.id_mesa}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-600">Cliente:</span>
                            <span class="font-medium">${pedido.nombre_cliente || 'Sin nombre'}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-600">Estado:</span>
                            <span class="font-medium ${getEstadoClass(pedido.estado)}">${pedido.estado}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-600">Tiempo transcurrido:</span>
                            <span class="font-medium">${tiempoTranscurrido} minutos</span>
                        </p>
                    </div>
                </div>

                <!-- Tiempos -->
                <div class="border-l pl-6">
                    <h4 class="font-bold text-lg mb-4">Tiempos</h4>
                    <div class="space-y-2">
                        <p class="flex justify-between">
                            <span class="text-gray-600">Hora pedido:</span>
                            <span class="font-medium">${formatearFecha(fechaPedido)}</span>
                        </p>
                        <p class="flex justify-between">
                            <span class="text-gray-600">Tiempo estimado:</span>
                            <span class="font-medium">${pedido.tiempo_estimado || 'No definido'} min</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Items del pedido -->
            <div class="mt-6">
                <h4 class="font-bold text-lg mb-4">Items del Pedido</h4>
                <div class="bg-white rounded-lg border">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Cantidad</th>
                                <th class="px-4 py-2 text-left">Item</th>
                                <th class="px-4 py-2 text-right">Precio Unit.</th>
                                <th class="px-4 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pedido.items.map(item => `
                                <tr class="border-t">
                                    <td class="px-4 py-2">${item.cantidad}x</td>
                                    <td class="px-4 py-2">${item.nombre}</td>
                                    <td class="px-4 py-2 text-right">$${parseFloat(item.precio).toFixed(2)}</td>
                                    <td class="px-4 py-2 text-right">$${(item.cantidad * item.precio).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr class="border-t font-bold">
                                <td colspan="3" class="px-4 py-2 text-right">Total:</td>
                                <td class="px-4 py-2 text-right">$${calcularTotal(pedido.items).toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Acciones -->
            <div class="mt-6 flex justify-end space-x-4">
                ${getAccionesPedido(pedido)}
            </div>
        </div>
    `;
}

// Funciones auxiliares
function getEstadoClass(estado) {
    switch (estado) {
        case 'pendiente': return 'text-red-600';
        case 'en preparación': return 'text-yellow-600';
        case 'completado': return 'text-green-600';
        default: return 'text-gray-600';
    }
}

function formatearFecha(fecha) {
    return fecha.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function calcularTotal(items) {
    return items.reduce((total, item) => total + (item.cantidad * parseFloat(item.precio)), 0);
}

// Función para crear los botones de acción según el estado
function crearBotonesAccion(pedido) {
    switch (pedido.estado) {
        case 'pendiente':
            return `
                <button onclick="iniciarPreparacion(${pedido.id})" 
                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                    Iniciar Preparación
                </button>
            `;
        case 'en preparación':
            return `
                <button onclick="marcarCompleto(${pedido.id})"
                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
                    Marcar Listo
                </button>
            `;
        case 'completado':
            return `
                <button onclick="entregarPedido(${pedido.id})"
                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                    Entregado
                </button>
            `;
        default:
            return '';
    }
}

function getAccionesPedido(pedido) {
    return crearBotonesAccion(pedido);
}

// Funciones para cambiar estados
function iniciarPreparacion(idPedido) {
    actualizarEstadoPedido(idPedido, 'en preparación');
}

function marcarCompleto(idPedido) {
    actualizarEstadoPedido(idPedido, 'completado');
}

function entregarPedido(idPedido) {
    actualizarEstadoPedido(idPedido, 'entregado');
}

function actualizarEstadoPedido(idPedido, nuevoEstado) {
    fetch('../Cocina/api/actualizar_estado_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id_pedido: idPedido,
            estado: nuevoEstado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarPedidos();
        } else {
            alert('Error al actualizar el estado del pedido');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Cargar pedidos inicialmente y actualizar cada 30 segundos
document.addEventListener('DOMContentLoaded', () => {
    cargarPedidos();
    setInterval(cargarPedidos, 30000);
});