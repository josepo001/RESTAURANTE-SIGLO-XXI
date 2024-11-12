// Cargar datos iniciales
function cargarDatos() {
    fetch('../Finanzas/api/obtener_datos_financieros.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('ingresos-hoy').textContent = `$${data.ingresosHoy}`;
                document.getElementById('egresos-hoy').textContent = `$${data.egresosHoy}`;
                document.getElementById('utilidad-hoy').textContent = `$${data.utilidadHoy}`;
                
                // Actualizar gráficos
                actualizarGraficos(data.graficos);
                
                // Actualizar tabla de movimientos
                actualizarTablaMovimientos(data.movimientos);
            } else {
                console.error('Error al cargar datos:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

function actualizarGraficos(datos) {
    // Gráfico de Ventas
    const ctxVentas = document.getElementById('grafico-ventas').getContext('2d');
    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: datos.ventas.map(v => v.fecha),
            datasets: [{
                label: 'Ventas Diarias',
                data: datos.ventas.map(v => v.total),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Utilidad
    const ctxUtilidad = document.getElementById('grafico-utilidad').getContext('2d');
    new Chart(ctxUtilidad, {
        type: 'bar',
        data: {
            labels: datos.utilidad.map(u => u.fecha),
            datasets: [{
                label: 'Utilidad Diaria',
                data: datos.utilidad.map(u => u.utilidad),
                backgroundColor: 'rgb(54, 162, 235)',
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
}

function actualizarTablaMovimientos(movimientos) {
    const tabla = document.getElementById('tabla-movimientos');
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

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();

    // Manejar formulario de egresos
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
                e.target.reset(); // Limpiar formulario
            } else {
                alert('Error al registrar el egreso: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    });
});