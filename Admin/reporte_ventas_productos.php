<?php
require '../fpdf/fpdf.php';
require_once '../Admin/DB.php';

$db = getDB();
$stmt = $db->prepare("
    SELECT productos.nombre, SUM(detalle_pedidos.cantidad) AS cantidad_vendida, SUM(detalle_pedidos.cantidad * detalle_pedidos.precio_unitario) AS total_venta
    FROM detalle_pedidos
    JOIN productos ON detalle_pedidos.id_producto = productos.id
    GROUP BY productos.nombre
    ORDER BY total_venta DESC
");
$stmt->execute();
$ventas_productos = $stmt->get_result();

// Iniciar FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título del PDF
$pdf->Cell(0, 10, 'Reporte de Ventas por Producto', 0, 1, 'C');
$pdf->Ln(10);

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Producto', 1);
$pdf->Cell(40, 10, 'Cantidad Vendida', 1);
$pdf->Cell(40, 10, 'Total en Ventas', 1);
$pdf->Ln();

// Cuerpo de tabla
$pdf->SetFont('Arial', '', 12);
while ($row = $ventas_productos->fetch_assoc()) {
    $pdf->Cell(70, 10, $row['nombre'], 1);
    $pdf->Cell(40, 10, $row['cantidad_vendida'], 1);
    $pdf->Cell(40, 10, '$' . number_format($row['total_venta'], 2), 1);
    $pdf->Ln();
}

// Salida del PDF
$pdf->Output('D', 'ventas_productos.pdf');
?>
