<?php
require '../fpdf/fpdf.php';
require_once '../Admin/DB.php';

$db = getDB();
$stmt = $db->prepare("
    SELECT proveedores.nombre AS proveedor, pedidos_proveedores.fecha_pedido, pedidos_proveedores.estado, pedidos_proveedores.total
    FROM pedidos_proveedores
    JOIN proveedores ON pedidos_proveedores.id_proveedor = proveedores.id
    WHERE MONTH(pedidos_proveedores.fecha_pedido) = MONTH(CURRENT_DATE())
      AND YEAR(pedidos_proveedores.fecha_pedido) = YEAR(CURRENT_DATE())
    ORDER BY pedidos_proveedores.fecha_pedido DESC
");
$stmt->execute();
$pedidos_proveedores = $stmt->get_result();

// Iniciar FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título del PDF
$pdf->Cell(0, 10, 'Reporte de Pedidos a Proveedores (Mes Actual)', 0, 1, 'C');
$pdf->Ln(10);

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Proveedor', 1);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Cell(30, 10, 'Estado', 1);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Ln();

// Cuerpo de tabla
$pdf->SetFont('Arial', '', 12);
while ($row = $pedidos_proveedores->fetch_assoc()) {
    $pdf->Cell(50, 10, $row['proveedor'], 1);
    $pdf->Cell(40, 10, $row['fecha_pedido'], 1);
    $pdf->Cell(30, 10, $row['estado'], 1);
    $pdf->Cell(30, 10, '$' . number_format($row['total'], 2), 1);
    $pdf->Ln();
}

// Salida del PDF
$pdf->Output('D', 'pedidos_proveedores_mes_actual.pdf');
?>
