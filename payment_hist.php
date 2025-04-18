<?php
require_once("D:\\Xampp\\htdocs\\UTU\\fpdf186\\fpdf.php");

// Get the ID of the receipt from the query parameter
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the ID

    // Create a new PDF instance
    $pdf = new FPDF();
    $pdf->AddPage();

    // Connect to the database and fetch the record for the given ID
    $servername = "localhost";
    $username = "root"; 
    $password = "";
    $dbname = "project1"; 

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);  
    }

    // Fetch the record from the maintenance1 table using the ID
    $sql = "SELECT * FROM maintenance1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $record = $result->fetch_assoc();

        // Set the title font and position it
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'MAINTENANCE PAYMENT RECEIPT', 0, 1, 'C');
        $pdf->Ln(5);

        // Draw a horizontal line for separation
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(10);

        // Receipt Header Section
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Receipt No: ', 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, $record['id'], 0, 1);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Date: ', 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, date('d-m-Y'), 0, 1);
        $pdf->Ln(10);

        // Payment Details Section
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Payment Details', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Flat No: ', 0, 0);
        $pdf->Cell(0, 10, $record['flat_no'], 0, 1);
        $pdf->Cell(40, 10, 'Payment Duration: ', 0, 0);
        $pdf->Cell(0, 10, $record['payment_duration'], 0, 1);
        $pdf->Cell(40, 10, 'Amount: ', 0, 0);
        
        // Add Rupee symbol here (₹) before the amount
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, number_format($record['amount'], 2), 0, 1);
        $pdf->Cell(40, 10, 'Payment Mode: ', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $record['payment_method'], 0, 1);
        $pdf->Ln(10);

        // Dates Information Section
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Dates Information', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, 'Start Date: ', 0, 0);
        $pdf->Cell(0, 10, date('d-m-Y', strtotime($record['start_date'])), 0, 1);
        $pdf->Cell(40, 10, 'End Date: ', 0, 0);
        $pdf->Cell(0, 10, date('d-m-Y', strtotime($record['end_date'])), 0, 1);
        $pdf->Ln(10);

        // Draw a line after the payment details
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(10);

        // Footer Section
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Thank you for your payment!', 0, 1, 'C');
        $pdf->Cell(0, 10, 'For any inquiries, contact us at: [Your Contact Info]', 0, 1, 'C');
        $pdf->Ln(10);

        // Output the PDF to the browser
        $pdf->Output();
    } else {
        echo "Receipt not found.";
    }

    $conn->close();
} else {
    echo "Invalid request.";
}
?>
