<?php
require '../fpdf/fpdf.php';
require_once '../Admin/DB.php';

$db = getDB();
$stmt = $db->prepare("
    SELECT nombre, stock, stock_minimo
    FROM ingredientes
    ORDER BY stock ASC
");
$stmt->execute();
$inventario = $stmt->get_result();

// Iniciar FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título del PDF
$pdf->Cell(0, 10, 'Reporte de Inventario', 0, 1, 'C');
$pdf->Ln(10);

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Ingrediente', 1);
$pdf->Cell(30, 10, 'Stock', 1);
$pdf->Cell(30, 10, 'Stock Mínimo', 1);
$pdf->Ln();

// Cuerpo de tabla
$pdf->SetFont('Arial', '', 12);
while ($row = $inventario->fetch_assoc()) {
    $pdf->Cell(70, 10, $row['nombre'], 1);
    $pdf->Cell(30, 10, $row['stock'], 1);
    $pdf->Cell(30, 10, $row['stock_minimo'], 1);
    $pdf->Ln();
}

// Salida del PDF
$pdf->Output('D', 'reporte_inventario.pdf');
?>
