<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project1"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching logged-in user's details (assuming the email is passed as a URL parameter or from session)
$userEmail = $_GET['email']; // Or use session: $_SESSION['email'] if the user is logged in

// Query to fetch user details from the userlogin1 table
$sql = "SELECT name, email, number, flat FROM userlogin1 WHERE email = '$userEmail'"; 

// Execute query and check for errors
$result = $conn->query($sql);

// Check if query executed successfully
if ($result === false) {
    // If query fails, print error and stop execution
    die("Error in query execution: " . $conn->error);
}

// Check if data is found
if ($result->num_rows > 0) {
    // Fetching the user data
    $user_data = $result->fetch_assoc();
    $userName = $user_data['name'];
    $userEmail = $user_data['email'];
    $userNumber = $user_data['number'];
    $flatNo = $user_data['flat'];
} else {
    echo "User not found!";
    exit();
}

// Query to fetch data from admin_maintenance table
$sql = "SELECT maintenance_amount, payment_duration, start_date, end_date FROM admin_maintenance"; 
$result = $conn->query($sql);

// Check if query executed successfully
if ($result === false) {
    // If query fails, print error and stop execution
    die("Error in query execution: " . $conn->error);
}

// Check if data is found
if ($result->num_rows > 0) {
    // Fetching the data
    $maintenance_data = $result->fetch_assoc();
    $maintenanceAmount = $maintenance_data['maintenance_amount'];
    $paymentDuration = $maintenance_data['payment_duration'];
    $startDate = $maintenance_data['start_date'];
    $endDate = $maintenance_data['end_date'];

    $startDate = date('d-m-Y', strtotime($maintenance_data['start_date']));
    $endDate = date('d-m-Y', strtotime($maintenance_data['end_date']));
} else {
    echo "No maintenance data found!";
    exit();
}

// Email notification function
function sendEmailNotification($name, $email, $number) {
    // Use PHPMailer to send email
    require 'PHPMailer/PHPMailer.php';  
    require 'PHPMailer/Exception.php';  
    require 'PHPMailer/SMTP.php';       

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';                          // SMTP host
        $mail->SMTPAuth   = true;                                     
        $mail->Username   = 'ressiment@gmail.com';                   // SMTP username (replace with your own)
        $mail->Password   = 'llyn fmwo nkzj kzpk';                    // SMTP password (replace with your own)
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;  
        $mail->Port       = 465;                                     

        // Recipients
        $mail->setFrom($email, $name);  
        $mail->addAddress("bhavyapatel1216@gmail.com", 'Treasurer'); // Set recipient email

        // Content
        $mail->isHTML(true);                                          
        $mail->Subject = 'Maintenance Payment Status Update';
        $mail->Body    = "Payment status has been updated for a maintenance record:<br>Name: $name<br>Email: $email<br>Phone Number: $number";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $paymentMethod = $_POST['payment-method'];
    $onlineMethod = isset($_POST['online-method']) ? $_POST['online-method'] : '';
    $cardName = isset($_POST['card-name']) ? $_POST['card-name'] : '';
    $upiId = isset($_POST['upi-id']) ? $_POST['upi-id'] : '';

    // Insert the payment details into the maintenance table
    $startDateSQL = date('Y-m-d', strtotime($maintenance_data['start_date']));
    $endDateSQL = date('Y-m-d', strtotime($maintenance_data['end_date']));
    
    $insert_sql = "INSERT INTO maintenance (flat_no, name, phone, email, payment_duration, start_date, end_date, amount, payment_method) 
               VALUES ('$flatNo', '$userName', '$userNumber', '$userEmail', '$paymentDuration', '$startDateSQL', '$endDateSQL', '$maintenanceAmount', '$paymentMethod')";


    if ($conn->query($insert_sql) === TRUE) {
        sendEmailNotification($userName, $userEmail, $userNumber);
    } else {
        echo "Error: " . $conn->error;
    }
}
$sql_onepayment = "SELECT * FROM onepayment";
$result_onepayment = $conn->query($sql_onepayment);

// Check if query executed successfully
if ($result_onepayment === false) {
    die("Error in query execution: " . $conn->error);
}
// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    /* General Styles */
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f3f4f6;
    color: #333;
    display: flex;
    height: 100vh;
    overflow: hidden;
}

h1, h2, h3, p {
    margin: 0;
    padding: 0;
}

h1 {
    font-size: 28px;
    color: #333;
    font-weight: bold;
}

/* Sidebar Styles */
.sidebar {
    width: 60px;
    background-color: #34495e;
    color: white;
    padding: 30px 20px;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    transition: width 0.3s ease;
    display: flex;
    flex-direction: column;

}

.sidebar:hover {
    width: 275px;
}

.sidebar-header {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar-header h2 {
    font-size: 22px;
    font-weight: bold;
    color: white;
}

.sidebar-menu {
    list-style-type: none;
    opacity: 0;
    transition: opacity 0.3s ease;
    margin-top: 20px;
    font-weight: bold;
}

.sidebar:hover .sidebar-menu {
    opacity: 1;
}

.sidebar-menu li {
    margin-top:20px;
}

.sidebar-menu li a {
    text-decoration: none;
    color: white;
    font-size: 18px;
    display: block;
    padding: 12px;
    border-radius: 5px;
    transition: background-color 0.3s ease, padding-left 0.3s ease;
}

.sidebar-menu li a:hover {
    background-color: #2980B9;
    padding-left: 20px;
}

/* Main Content Styles */
.main-content {
    margin-left: 60px;
    padding: 30px;
    flex-grow: 1;
    background-color: #ffffff;
    height: 100vh;
    overflow-y: auto;
    transition: margin-left 0.3s ease;
}

.sidebar:hover ~ .main-content {
    margin-left: 275px;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #a3c9f1;
    color: white;
    padding: 10px 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: -15px;
}

.header h1 {
    margin: 0;
    font-size: 28px;
}

.logout {
    background: #e74c3c;
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.logout:hover {
    background: #c0392b;
}

/* Payment Form Styles */
.payment-options {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
    font-size: 16px;
}

.payment-options h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
}

.payment-method {
    margin-bottom: 20px;
}

.payment-method label {
    font-size: 18px;
    color: #333;
    display: block;
    margin-bottom: 10px;
}

.payment-method select {
    width: 100%;
    padding: 12px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

.payment-method select:focus {
    border-color: #2980B9;
    background-color: #ffffff;
}

#online-methods {
    display: none;
    margin-top: 20px;
}

#online-methods label {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
}

#online-methods select {
    width: 40%;
    padding: 12px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
}

#online-methods select:focus {
    border-color: #2980B9;
    background-color: #ffffff;
}

#card-info, #upi-info {
    margin-top: 20px;
    display: none;
}

#card-info input, #upi-info input {
    width: 40%;
    padding: 12px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
    margin-top: 10px;
    background-color: #f9f9f9;
}

#card-info input:focus, #upi-info input:focus {
    border-color: #2980B9;
    background-color: #ffffff;
}

/* Submit Button */
button[type="submit"] {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 20px;
    width: 40%;
}

button[type="submit"]:hover {
    background-color: #218838;
}

/* Payment Info Section */
.maintenance-info {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 30px;
    border: 1px solid #ddd;
    font-size: 18px;
}

.maintenance-info h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
    font-weight: bold;
}

.maintenance-info p {
    margin-bottom: 12px;
    line-height: 1.5;
    color: #555;
}

.maintenance-info .font-bold {
    font-weight: bold;
}

#total-amount {
    font-size: 20px;
    color: #2ecc71;
    margin-top: 15px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .sidebar {
        width: 60px;
    }

    .sidebar:hover {
        width: 250px;
    }

    .main-content {
        margin-left: 60px;
        padding: 20px;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header h1 {
        font-size: 24px;
    }

    .payment-options {
        padding: 20px;
    }

    .payment-method select {
        font-size: 14px;
    }

    button[type="submit"] {
        padding: 12px 20px;
    }
}
    
</style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="sidebar-menu">      
            <h2 style="text-align: center;font-size:28px;"><?php echo htmlspecialchars($userName); ?> DashBoard</h2>
            <li><a href="rental_profile.php?email=<?php echo urlencode($userEmail); ?>">👤 Profile</a></li>
            <li><a href="#">📈 C</a></li>
            <li><a href="maintenance.php?email=<?php echo urlencode($user[$userEmail]); ?>">💸 Payment</a></li>
            <li><a href="#">💬 E</a></li>
            <li><a href="loginpage.php">⬅️ Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>💸 Maintenance Payment</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>
        <form method="POST">
            <div class="maintenance-info">
                <h2>Maintenance Fee</h2>
                <p>The maintenance fee for the flat is set at <strong>₹<?php echo $maintenanceAmount; ?></strong> per month.</p>
                <table class="w-full text-left">
                    <tr>
                        <td id="total-amount" class="font-bold text-lg"><strong>Total Amount:</strong></td>
                        <td id="total-amount" class="font-bold text-lg">₹<?php echo $maintenanceAmount; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Duration:</strong></td>
                        <td><?php echo $paymentDuration; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Starting Date:</strong></td>
                        <td><?php echo $startDate; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Ending Date:</strong></td>
                        <td><?php echo $endDate; ?></td>
                    </tr>
                </table>
            </div>
            <!-- One-Time Payment Details -->
            <div class="maintenance-info">
                <h2>One-Time Payment Details</h2>
                <table class="w-full text-left">
                    <tr>
                        <th>Payment Reason</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                       
                    </tr>
                    <?php if ($result_onepayment->num_rows > 0): ?>
                        <?php while($row = $result_onepayment->fetch_assoc()): ?>
                            
                            <tr>   
                                <td><?php echo $row['payment_reason']; ?></td>
                                <td><?php echo $pd = date("d-m-Y", strtotime($row['payment_date'])); ?></td>
                                <td>₹<?php echo $row['one_time_amount']; ?></td>
                                
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No one-time payments found.</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
            <div class="payment-options">
                <div class="form-group">
                    <label for="payment-method">Payment Method:</label>
                    <select id="payment-method" name="payment-method" onchange="updatePaymentMethod()" required>
                        <option value="cash">Cash</option>
                        <option value="online">Online Payment</option>
                    </select>

                    <div id="online-methods" style="display:none;">
                        <label for="online-method">Choose Online Method:</label>
                        <select id="online-method" name="online-method">
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                        </select>

                        <div id="card-info" style="display:none;">
                            <label for="card-name">Card Name:</label>
                            <input type="text" id="card-name" name="card-name" value="<?php echo $maintenance_data['card_name']; ?>">
                        </div>
                        <div id="upi-info" style="display:none;">
                            <label for="upi-id">UPI ID:</label>
                            <input type="text" id="upi-id" name="upi-id" value="<?php echo $maintenance_data['upi_id']; ?>">
                        </div>
                    </div>
                </div>

                <button type="submit">Submit Payment</button>
            </div>
        </form>
    </div>

    <script>
        // Toggle display of online methods based on payment method selection
        function updatePaymentMethod() {
            var paymentMethod = document.getElementById('payment-method').value;
            var onlineMethods = document.getElementById('online-methods');
            if (paymentMethod === 'online') {
                onlineMethods.style.display = 'block';
            } else {
                onlineMethods.style.display = 'none';
            }
        }

        // Initial state when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            updatePaymentMethod();
        });
    </script>
</body>
</html>
