<?php
require_once "config.php";
require_once "vendor/autoload.php";

// Prevent any output before PDF generation
ob_clean();

$course_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
$course_name = '';
$course_code = '';

if ($course_id) {
    $sql = "SELECT * FROM courses WHERE course_id = '$course_id'";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $course_name = $row['course_name'];
        $course_code = $row['course_code'];
    }
}

try {
    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Attendance System');
    $pdf->SetAuthor('Attendance System');
    $pdf->SetTitle('Attendance Report - ' . $course_name);

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins
    $pdf->SetMargins(15, 15, 15);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', 'B', 16);

    // Title
    $pdf->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
    $pdf->Ln(5);

    // Course Information
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Course Information:', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Course Name: ' . $course_name, 0, 1);
    $pdf->Cell(0, 10, 'Course Code: ' . $course_code, 0, 1);
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(30, 10, 'Student ID', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Student Name', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Date', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Status', 1, 1, 'C');

    // Table data
    $pdf->SetFont('helvetica', '', 10);
    
    // Get attendance data
    $sql = "SELECT a.*, s.first_name, s.last_name 
            FROM attendance a 
            JOIN students s ON a.student_id = s.student_id 
            WHERE a.course_id = '$course_id' 
            ORDER BY a.date DESC, s.first_name, s.last_name";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(30, 10, $row['student_id'], 1, 0, 'C');
        $pdf->Cell(60, 10, $row['first_name'] . ' ' . $row['last_name'], 1, 0, 'L');
        $pdf->Cell(40, 10, $row['date'], 1, 0, 'C');
        $pdf->Cell(40, 10, ucfirst($row['status']), 1, 1, 'C');
    }

    // Output PDF
    $pdf->Output('attendance_report_' . $course_code . '.pdf', 'D');
    exit;
} catch (Exception $e) {
    die('Error generating PDF: ' . $e->getMessage());
} 