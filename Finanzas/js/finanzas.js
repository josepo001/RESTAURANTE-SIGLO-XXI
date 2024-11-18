// Función para cargar los datos iniciales
function cargarDatos() {
    fetch('../Finanzas/api/obtener_datos_financieros.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar los valores de ingresos, egresos y utilidad
                document.getElementById('ingresos-hoy').textContent = `$${data.ingresosHoy}`;
                document.getElementById('egresos-hoy').textContent = `$${data.egresosHoy}`;
                document.getElementById('utilidad-hoy').textContent = `$${data.utilidadHoy}`;

                // Actualizar gráficos
                actualizarGraficos(data.graficos);

                // Actualizar la tabla de movimientos
                actualizarTablaMovimientos(data.movimientos);
            } else {
                console.error('Error al cargar datos:', data.error);
            }
        })
        .catch(error => console.error('Error en la solicitud:', error));
}

// Función para actualizar los gráficos
function actualizarGraficos(datos) {
    // Verificar si existen datos válidos
    if (!datos || !datos.ventas || !datos.utilidad) {
        console.error('Datos insuficientes para los gráficos');
        return;
    }

    // Gráfico de Ventas del Mes
    const ctxVentas = document.getElementById('grafico-ventas').getContext('2d');
    new Chart(ctxVentas, {
        type: 'bar',
        data: {
            labels: datos.ventas.map(v => v.fecha), // Fechas de las ventas
            datasets: [{
                label: 'Ventas ($)',
                data: datos.ventas.map(v => v.total), // Totales de ventas
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => `$${value.toFixed(2)}`
                    }
                }
            }
        }
    });

    // Gráfico de Utilidad Mensual
    const ctxUtilidad = document.getElementById('grafico-utilidad').getContext('2d');
    new Chart(ctxUtilidad, {
        type: 'line',
        data: {
            labels: datos.utilidad.map(u => u.fecha), // Fechas de utilidad
            datasets: [{
                label: 'Utilidad ($)',
                data: datos.utilidad.map(u => u.utilidad), // Totales de utilidad
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => `$${value.toFixed(2)}`
                    }
                }
            }
        }
    });
}

// Función para actualizar la tabla de movimientos
function actualizarTablaMovimientos(movimientos) {
    const tabla = document.getElementById('tabla-movimientos');
    if (!movimientos || movimientos.length === 0) {
        tabla.innerHTML = '<tr><td colspan="4" class="text-center py-4">No hay movimientos recientes</td></tr>';
        return;
    }

    tabla.innerHTML = movimientos.map(mov => `
        <tr class="border-b">
            <td class="px-6 py-4">${mov.fecha}</td>
            <td class="px-6 py-4">${mov.tipo === 'ingreso_venta' ? 'Ingreso' : 'Egreso'}</td>
            <td class="px-6 py-4">${mov.concepto}</td>
            <td class="px-6 py-4 ${mov.tipo === 'ingreso_venta' ? 'text-green-600' : 'text-red-600'}">
                $${parseFloat(mov.monto).toFixed(2)}
            </td>
        </tr>
    `).join('');
}

// Inicializar al cargar el documento
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();

    // Manejo del formulario de egresos
    document.getElementById('form-egreso').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        fetch('../Finanzas/api/registrar_egreso.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Egreso registrado correctamente');
                cargarDatos(); // Recargar datos
                e.target.reset(); // Limpiar el formulario
            } else {
                alert('Error al registrar el egreso: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al procesar la solicitud:', error);
            alert('Error al procesar la solicitud');
        });
    });
});
