<?php
try {
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "project1";

    // Create a connection to the database
    $conn = mysqli_connect($hostname, $username, $password, $database);

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Validate email
    $userEmail = filter_var($_GET['email'] ?? $_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    if (!$userEmail) {
        echo "Invalid email!";
        exit();
    }

    // Fetch user details based on the email
    $sql = "SELECT name, email, number, flat FROM userlogin1 WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $userName = $user_data['name'];
        $userEmail = $user_data['email'];
        $userNumber = $user_data['number'];
        $flatNo = $user_data['flat'];
    } else {
        echo "User not found!";
        exit();
    }

    // Fetch one-time payment details from the onepayment table
    $sql = "SELECT payment_reason, one_time_amount, payment_date FROM onepayment";
    $result_onepayment = mysqli_query($conn, $sql);

    if ($result_onepayment->num_rows > 0) {
        // Fetch first record (or you can modify if you want to fetch multiple records)
        $payment_data = mysqli_fetch_assoc($result_onepayment);
        $payment_reason = $payment_data['payment_reason'];
        $payment_amount = $payment_data['one_time_amount'];
    } else {
        echo "No one-time payment details found.";
        exit();
    }

    // Handle the form submission
    $paymentSuccess = false; // Flag initialization
    $paymentExists = false; // Flag to track if payment already exists
    $paymentError = false; // Flag to track payment failure

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if payment already exists for this flat and date
        $check_payment_sql = "SELECT * FROM onetimepayment WHERE flat_no = ? AND payment_date = ?";
        $stmt_check = $conn->prepare($check_payment_sql);
        $paymentDate = date('Y-m-d');
        $stmt_check->bind_param("ss", $flatNo, $paymentDate);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            // Payment already exists for this date
            $paymentExists = true;
        } else {
            // Insert into onetimepayment table
            $insert_sql = "INSERT INTO onetimepayment (flat_no, name, phone, email, payment_reason, payment_date, one_time_amount, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssssss", $flatNo, $userName, $userNumber, $userEmail, $payment_reason, $paymentDate, $payment_amount);

            if ($stmt->execute()) {
                $paymentSuccess = true; // Flag to show success message
            } else {
                $paymentError = true; // Flag to show error message
            }
        }
    }
} catch (Exception $e) {
    echo $e;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Time Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
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
            margin-top: 20px;
            font-size: 16px;
            width:50%;
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

        /* One-Time Payment Button Styles */
        .one-time-payment-btn-container {
            margin-top: 20px;
            margin-left:10px;
        }

        .one-time-payment-btn {
            background-color: green;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .one-time-payment-btn:hover {
            background-color: #2ecc71;
        }
        /* Success Message Styles */
.payment-success-message {
    text-align: center;
    margin-top: 20px;
}

.success-message {
    background-color: #28a745;
    color: white;
    padding: 15px;
    font-size: 18px;
    border-radius: 5px;
    width: 20%;
    margin: 0 auto;
    font-weight: bold;
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
            <li><a href="u_profile.php?email=<?php echo urlencode($userEmail); ?>">👤 Profile</a></li>
            <li><a href="onboarding.php?email=<?php echo urlencode($userEmail); ?>">📝 Onboarding</a></li>
            <li><a href="#">📈 C</a></li>
            <li><a href="maintenance.php?email=<?php echo urlencode($userEmail); ?>">💸 Payment</a></li>
            <li><a href="#">💬 E</a></li>
            <li><a href="loginpage.php">⬅️ Logout</a></li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>💸 One-Time Payment</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <!-- Display Success Message -->
        <?php if (isset($paymentSuccess) && $paymentSuccess): ?>
            <div class="payment-success-message mt-4 flex justify-center">
                <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center space-x-3 max-w-xs">
                    <i class="fas fa-check-circle text-2xl"></i>
                    <p class="font-semibold">Payment Successfully Completed!</p>
                </div>
            </div>
        <?php elseif (isset($paymentExists) && $paymentExists): ?>
            <div class="payment-success-message mt-4 flex justify-center">
                <div class="bg-yellow-500 text-white p-4 rounded-lg shadow-lg flex items-center space-x-3 max-w-xs">
                    <i class="fas fa-exclamation-circle text-2xl"></i>
                    <p class="font-semibold">Payment already exists for this duration.</p>
                </div>
            </div>
        <?php elseif (isset($paymentError) && $paymentError): ?>
            <div class="payment-success-message mt-4 flex justify-center">
                <div class="bg-red-500 text-white p-4 rounded-lg shadow-lg flex items-center space-x-3 max-w-xs">
                    <i class="fas fa-times-circle text-2xl"></i>
                    <p class="font-semibold">Payment failed. Please try again.</p>
                </div>
            </div>
        <?php endif; ?>
        <div class="one-time-payment-btn-container">
            <a href="maintenance.php?email=<?php echo urlencode($userEmail); ?>">
                <button class="one-time-payment-btn">Maintenance Payment</button>
            </a>
            <a href="one_time_payment.php?email=<?php echo urlencode($userEmail); ?>">
                <button class="one-time-payment-btn">One Time Payment</button>
            </a>
        </div>

        <form method="POST" action="one_time_payment.php?email=<?php echo urlencode($userEmail); ?>">
            <div class="maintenance-info">
                <h2>One-Time Payment Details</h2>

                <!-- Displaying the one-time payment details in a table -->
                <table class="w-full text-left" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border border-gray-300 text-left"><strong>Reason</strong></th>
                            <th class="px-4 py-2 border border-gray-300 text-left"><strong>Date</strong></th>
                            <th class="px-4 py-2 border border-gray-300 text-left"><strong>Amount</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_onepayment->num_rows > 0) : ?>
                            <tr>
                                <td class="px-4 py-2 border border-gray-300"><?php echo htmlspecialchars($payment_reason); ?></td>
                                <td class="px-4 py-2 border border-gray-300"><?php echo date('d-F-Y', strtotime($payment_data['payment_date'])); ?></td>
                                <td class="px-4 py-2 border border-gray-300">₹<?php echo htmlspecialchars($payment_amount); ?></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="px-4 py-2 border border-gray-300 text-center">No One Time Payment found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Payment Options Form -->
                <div class="payment-options">
                    <input type="hidden" name="email" value="<?php echo urlencode($userEmail); ?>" />
                    <button type="submit" name="pay_button" class="px-4 py-2 bg-blue-500 text-white rounded">Pay</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
