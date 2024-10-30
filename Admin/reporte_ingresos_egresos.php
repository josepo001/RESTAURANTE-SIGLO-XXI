<?php
require '../fpdf/fpdf.php';
require_once '../Admin/DB.php';

$db = getDB();
$stmt = $db->prepare("
    SELECT 
        SUM(CASE WHEN tipo_transaccion = 'ingreso_venta' THEN ingresos ELSE 0 END) AS total_ingresos,
        SUM(CASE WHEN tipo_transaccion = 'egreso_compra' THEN egresos ELSE 0 END) AS total_egresos
    FROM finanzas
");
$stmt->execute();
$finanzas = $stmt->get_result()->fetch_assoc();
$utilidad_neta = $finanzas['total_ingresos'] - $finanzas['total_egresos'];

// Iniciar FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título del PDF
$pdf->Cell(0, 10, 'Reporte de Ingresos y Egresos', 0, 1, 'C');
$pdf->Ln(10);

// Datos del reporte
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, 'Ingresos:', 0, 0);
$pdf->Cell(50, 10, '$' . number_format($finanzas['total_ingresos'], 2), 0, 1);
$pdf->Cell(50, 10, 'Egresos:', 0, 0);
$pdf->Cell(50, 10, '$' . number_format($finanzas['total_egresos'], 2), 0, 1);
$pdf->Cell(50, 10, 'Utilidad Neta:', 0, 0);
$pdf->Cell(50, 10, '$' . number_format($utilidad_neta, 2), 0, 1);

// Salida del PDF
$pdf->Output('D', 'ingresos_egresos.pdf');
?>
