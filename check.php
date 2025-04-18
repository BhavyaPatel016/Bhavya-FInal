<?php
// Define functions first

// Email notification function SENDER
function sendEmailNotification($name, $email, $number, $filePath) {
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ressiment@gmail.com'; // Your email address
        $mail->Password   = 'llyn fmwo nkzj kzpk'; // Your email password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('ressiment@gmail.com', 'Maintenance Team');
        $mail->addAddress($email, 'User');

        // Attach the PDF receipt
        $mail->addAttachment($filePath, 'Receipt_.pdf');

        $mail->isHTML(true);
        $mail->Subject = 'Maintenance Payment Status Update';
        $mail->Body    = "Dear $name,<br><br>Your payment for maintenance has been successfully updated. Please find the receipt attached.<br><br>Thank you!<br>Regards,<br>Maintenance Team";

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function generateReceiptPDF($record) {
    require_once("D:\\Xampp\\htdocs\\UTU\\fpdf186\\fpdf.php");
    $pdf = new FPDF();
    $pdf->AddPage();

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

    // Save the PDF to a file
    $filePath = 'uploads/Receipt_' . $record['id'] .'_'. $record['name'] .'.pdf';
    $pdf->Output('F', $filePath);

    return $filePath; // Return the file path for attachment
}

try {
    $servername = "localhost";
    $username = "root"; // Change this to your database username
    $password = ""; // Change this to your database password
    $dbname = "project1"; // Your database name

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);  
    }

    // Handle "Received" action
    if (isset($_GET['received'])) {
        $id = intval($_GET['received']); // Sanitize ID
    
        // Fetch the record with this ID from maintenance
        $fetchStmt = $conn->prepare("SELECT * FROM maintenance WHERE id = ?");
        $fetchStmt->bind_param("i", $id);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();
    
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
    
            // Check if record already exists in maintenance1
            $checkStmt = $conn->prepare("SELECT id FROM maintenance1 WHERE id = ?");
            $checkStmt->bind_param("i", $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
    
            if ($checkResult->num_rows === 0) {
                // Insert into maintenance1
                $insertSql = "INSERT INTO maintenance1 (id, flat_no, name, phone, email, payment_duration, start_date, amount, end_date, payment_method)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
                $stmtInsert = $conn->prepare($insertSql);
                $stmtInsert->bind_param(
                    "isssssssss",
                    $record['id'], 
                    $record['flat_no'], 
                    $record['name'], 
                    $record['phone'], 
                    $record['email'], 
                    $record['payment_duration'], 
                    $record['start_date'], 
                    $record['amount'], 
                    $record['end_date'], 
                    $record['payment_method']
                );
    
                if ($stmtInsert->execute()) {
                    // Update original table, generate PDF, send email
                    $updateStmt = $conn->prepare("UPDATE maintenance SET status = 'received' WHERE id = ?");
                    $updateStmt->bind_param("i", $id);
                    $updateStmt->execute();
    
                    $filePath = generateReceiptPDF($record);
                    sendEmailNotification($record['name'], $record['email'], $record['phone'], $filePath);
    
                    header("Location: check.php?status=received");
                    exit;
                } else {
                    die("Error inserting into maintenance1: " . $conn->error);
                }
            } else {
                echo "This record has already been processed.";
            }
        } else {
            echo "No record found with this ID.";
        }
    }
    
    // Handle "Unreceived" action
    if (isset($_GET['unreceived'])) {
        $id = intval($_GET['unreceived']); // Sanitize ID

        // Update the status of the record in the maintenance table
        $updateSql = "UPDATE maintenance SET status = 'rejected' WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("i", $id);

        if ($stmtUpdate->execute()) {
            header("Location: check.php?status=unreceived");
            exit;
        } else {
            die("Error updating status: " . $conn->error);
        }
    }

    // Fetch all pending records
    $sql = "SELECT * FROM maintenance WHERE status = 'pending'";
    $result = $conn->query($sql);

    $pendingUsers = [];
    if ($result->num_rows > 0) {
        $pendingUsers = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch(Exception $e) {
    echo $e;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Notification</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Global styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #eef2f3, #e3f2fd); /* Light gradient background */
            color: #333;
            display: flex;
            transition: background 0.3s ease;
        }

        .sidebar {
            width: 220px;
            background: #2C3E50;
            color: white;
            height: 100vh;
            padding: 40px 30px;
            position: fixed;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
            border-radius: 0 20px 20px 0; /* Rounded corners */
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 60px;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #FFDC00;
            text-transform: uppercase;
        }

        /* Sidebar Links */
        .sidebar a {
            display: block;
            padding: 15px 20px;
            margin: 10px 0;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 5px;
            height: 100%;
            background-color: #FFDC00;
            border-radius: 5px;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar a:hover {
            background: #34495E; 
            transform: translateX(10px);
        }

        .sidebar a:hover::before {
            opacity: 1;
        }
        /* Main Content Styles */
        .main-content {
            margin-left: 270px;
            padding: 30px;
            flex-grow: 1;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            transition: margin-left 0.3s ease;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2C3E50;
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header .logout {
            background: #E74C3C;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s, transform 0.3s;
            cursor: pointer;
        }

        .header .logout:hover {
            background: #C0392B;
            transform: scale(1.1);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Button Styles */
        .approve-button, .reject-button {
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            text-align: center;
        }

        .approve-button {
            background-color: #4CAF50;
        }

        .reject-button {
            background-color: #f44336;
        }

        .approve-button:hover {
            background-color: #45a049;
        }

        .reject-button:hover {
            background-color: #d32f2f;
        }

        /* No data found */
        .no-record {
            text-align: center;
            color: #666;
            font-size: 16px;
        }

        td .button-container {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 220px;
            }

            .sidebar h2 {
                font-size: 22px;
            }

            .sidebar a {
                font-size: 16px;
                padding: 12px 15px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .header h1 {
                font-size: 28px;
            }

            .header .logout {
                font-size: 14px;
                padding: 10px 15px;
            }
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .main-content {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>📩Check Payment</h2>
        <a href="t_profile.php" class="active">👤 Profile</a>
        <a href="check.php">📩 Check Payment</a>
        <a href="m_report.php">📊 Maintenance Reports</a>
        <a href="onetimepayment.php">💰One Time Payment</a>
        <a href="onetime_report.php">💰One Time Report</a>
        <a href="main_history.php">🛠️Maintenance History</a>
        <a href="loginpage.php">⬅️ Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Pending Maintenance Notifications</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Flat Number</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Payment Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Confirm Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendingUsers)): ?>
                    <tr>
                        <td colspan="10" class="no-record">No pending maintenance records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pendingUsers as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['flat_no']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['payment_duration']) ?></td>
                            <td><?= date('d-m-Y', strtotime($user['start_date'])) ?></td>
                            <td><?= date('d-m-Y', strtotime($user['end_date'])) ?></td>
                            <td><?= htmlspecialchars($user['amount']) ?></td>
                            <td><?= htmlspecialchars($user['payment_method']) ?></td>
                            <td>
                                <div class="button-container">
                                    <a href="?received=<?= $user['id'] ?>" class="approve-button">Received</a>
                                    <a href="?unreceived=<?= $user['id'] ?>" class="reject-button">Unreceived</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
