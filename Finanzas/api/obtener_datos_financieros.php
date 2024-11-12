<?php
header('Content-Type: application/json');

try {
    $host = 'localhost';
    $dbname = 'ene';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener fecha actual
    $hoy = date('Y-m-d');
    $primerDiaMes = date('Y-m-01');
    $ultimoDiaMes = date('Y-m-t');

    // Ingresos del día (desde tabla pedidos)
    $stmtIngresos = $conn->prepare("
        SELECT COALESCE(SUM(total), 0) as total
        FROM pedidos 
        WHERE DATE(fecha_pedido) = :fecha 
        AND estado != 'cancelado'
    ");
    $stmtIngresos->execute(['fecha' => $hoy]);
    $ingresosHoy = $stmtIngresos->fetch(PDO::FETCH_ASSOC)['total'];

    // Egresos del día (desde tabla finanzas)
    $stmtEgresos = $conn->prepare("
        SELECT COALESCE(SUM(egresos), 0) as total
        FROM finanzas 
        WHERE fecha = :fecha 
        AND tipo_transaccion = 'egreso_compra'
    ");
    $stmtEgresos->execute(['fecha' => $hoy]);
    $egresosHoy = $stmtEgresos->fetch(PDO::FETCH_ASSOC)['total'];

    // Datos para el gráfico de ventas mensuales
    $stmtVentasMes = $conn->prepare("
        SELECT DATE(fecha_pedido) as fecha, 
               COALESCE(SUM(total), 0) as total
        FROM pedidos
        WHERE DATE(fecha_pedido) BETWEEN :inicio AND :fin
        AND estado != 'cancelado'
        GROUP BY DATE(fecha_pedido)
        ORDER BY DATE(fecha_pedido)
    ");
    $stmtVentasMes->execute([
        'inicio' => $primerDiaMes,
        'fin' => $ultimoDiaMes
    ]);
    $ventasMes = $stmtVentasMes->fetchAll(PDO::FETCH_ASSOC);

    // Datos para el gráfico de utilidad mensual
    // Primero obtenemos los ingresos por día
    $stmtIngresosDiarios = $conn->prepare("
        SELECT DATE(fecha_pedido) as fecha, 
               COALESCE(SUM(total), 0) as ingresos
        FROM pedidos
        WHERE DATE(fecha_pedido) BETWEEN :inicio AND :fin
        AND estado != 'cancelado'
        GROUP BY DATE(fecha_pedido)
    ");
    $stmtIngresosDiarios->execute([
        'inicio' => $primerDiaMes,
        'fin' => $ultimoDiaMes
    ]);
    $ingresosDiarios = $stmtIngresosDiarios->fetchAll(PDO::FETCH_ASSOC);

    // Luego obtenemos los egresos por día
    $stmtEgresosDiarios = $conn->prepare("
        SELECT fecha, COALESCE(SUM(egresos), 0) as egresos
        FROM finanzas
        WHERE fecha BETWEEN :inicio AND :fin
        AND tipo_transaccion = 'egreso_compra'
        GROUP BY fecha
    ");
    $stmtEgresosDiarios->execute([
        'inicio' => $primerDiaMes,
        'fin' => $ultimoDiaMes
    ]);
    $egresosDiarios = $stmtEgresosDiarios->fetchAll(PDO::FETCH_ASSOC);

    // Combinamos los ingresos y egresos para calcular la utilidad
    $utilidadMes = [];
    foreach ($ingresosDiarios as $ingreso) {
        $fecha = $ingreso['fecha'];
        $montoEgreso = 0;
        foreach ($egresosDiarios as $egreso) {
            if ($egreso['fecha'] == $fecha) {
                $montoEgreso = $egreso['egresos'];
                break;
            }
        }
        $utilidadMes[] = [
            'fecha' => $fecha,
            'utilidad' => $ingreso['ingresos'] - $montoEgreso
        ];
    }

    // Obtener movimientos recientes combinando pedidos y finanzas
    $stmtMovimientos = $conn->prepare("
        (SELECT 
            DATE(fecha_pedido) as fecha,
            'ingreso_venta' as tipo,
            CONCAT('Pedido #', id, ' - ', COALESCE(nombre_cliente, 'Cliente sin nombre')) as concepto,
            total as monto
        FROM pedidos
        WHERE estado != 'cancelado'
        AND fecha_pedido >= DATE_SUB(NOW(), INTERVAL 30 DAY))
        UNION ALL
        (SELECT 
            fecha,
            tipo_transaccion as tipo,
            CASE 
                WHEN id_proveedor IS NOT NULL THEN CONCAT('Compra a proveedor #', id_proveedor)
                ELSE 'Egreso manual'
            END as concepto,
            egresos as monto
        FROM finanzas
        WHERE tipo_transaccion = 'egreso_compra'
        AND fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))
        ORDER BY fecha DESC, tipo DESC
        LIMIT 10
    ");
    $stmtMovimientos->execute();
    $movimientos = $stmtMovimientos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'ingresosHoy' => number_format($ingresosHoy, 2),
        'egresosHoy' => number_format($egresosHoy, 2),
        'utilidadHoy' => number_format($ingresosHoy - $egresosHoy, 2),
        'graficos' => [
            'ventas' => $ventasMes,
            'utilidad' => $utilidadMes
        ],
        'movimientos' => $movimientos
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>