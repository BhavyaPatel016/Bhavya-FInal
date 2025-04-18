<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql_check = "SELECT COUNT(*) as count FROM admin_maintenance";
$result = $conn->query($sql_check);
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    // If an entry exists, redirect to m_admin.php
    header("Location: m_admin.php");
    exit();
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_duration = $_POST['payment-duration'];
    $s_date = $_POST['s_date'];
    $e_date = $_POST['e_date'];
    $maintenance_amount = $_POST['maintenance-amount'];  // Get the maintenance amount from user input

    // Validate positive maintenance amount
    if ($maintenance_amount <= 0) {
        echo "Please enter a positive value for the maintenance amount.";
        exit();
    }

    // Define a static amount to calculate the payment duration portion
    $monthly_amount = $maintenance_amount; // Use maintenance amount as the base for monthly calculation
    $total_amount = 0;

    // Calculate total amount based on payment duration
    if (isset($payment_duration)) {
        if (in_array('1-month', $payment_duration)) {
            $total_amount += $monthly_amount * 1;
        }
        if (in_array('3-months', $payment_duration)) {
            $total_amount += $monthly_amount * 3;
        }
        if (in_array('6-months', $payment_duration)) {
            $total_amount += $monthly_amount * 6;
        }
        if (in_array('1-year', $payment_duration)) {
            $total_amount += $monthly_amount * 12;
        }
    }

    // Prepare the SQL query with placeholders
    $stmt = $conn->prepare("INSERT INTO admin_maintenance (payment_duration, start_date, end_date, total_amount, maintenance_amount) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);  // Display the specific MySQL error
    }

    // Convert the payment duration array into a comma-separated string
    $payment_duration_str = implode(', ', $payment_duration);

    // Bind the parameters to the query
    $stmt->bind_param("ssssi", $payment_duration_str, $s_date, $e_date, $total_amount, $maintenance_amount);

    // Execute the query
    if ($stmt->execute()) {
        // Success, redirect to m_admin.php
        header("Location: m_admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Maintenance</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* General Body Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #eef2f3, #e3f2fd); /* Light gradient background */
            color: #333;
            display: flex;
            height: 100vh;
            overflow-x: hidden;
            transition: background 0.3s ease;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 270px;
            background: #2C3E50; /* Dark shade for sidebar */
            color: white;
            height: 100%;
            padding: 40px 30px;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            border-radius: 0 20px 20px 0; /* Rounded corners for the sidebar */
            z-index: 10;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 60px;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #FFDC00; /* Bright color for the title */
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

        .sidebar a.active {
            background: #2980B9; /* Highlight active link */
            box-shadow: 0 5px 15px rgba(41, 128, 185, 0.4);
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

        .main-content .header {
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

        /* Form and Input Styles */
        form {
            margin-top: 30px;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }

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
            width: 25%;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        /* Checkbox Styles */
        .checkbox-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .checkbox-group input[type="checkbox"] {
            appearance: none;
            background-color: #fff;
            border: 2px solid #ccc;
            border-radius: 4px;
            width: 20px;
            height: 20px;
            transition: background-color 0.3s ease;
        }

        .checkbox-group input[type="checkbox"]:checked {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .checkbox-group label {
            font-size: 16px;
            font-weight: normal;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"]:checked + label {
            color: #4CAF50;
        }

        /* Date Input Styles */
        input[type="date"] {
            width: 150px;
            padding: 8px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        /* Specific Maintenance Amount Input */
        input#maintenance-amount {
            width: 50%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #fafafa;
            color: #333;
            margin: 5px 0;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s ease;
        }

        input#maintenance-amount:focus {
            border-color: #6793AC;
            box-shadow: 0 0 5px rgba(103, 147, 172, 0.5);
            outline: none;
        }

        /* Previous Button */
        .prev-button {
            position: fixed;
            bottom: 10px;
            right: 30px;
            background-color: green;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        .prev-button:hover {
            background-color:#218838;
        }

        .prev-button:focus {
            outline: none;
        }

        /* Flexbox Layout for Forms */
        .form-group-horizontal {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .form-item {
            flex: 1;
            min-width: 200px;
            margin-right: 10px; /* Reduced gap between form fields */
        }

        /* Animations */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .main-content {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive Styles */
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

    </style>
</head>
<body>
    <div class="sidebar">
        <h2 style="font-size:20px;">🔧Set Maintenance</h2>
        <a href="t_profile.php" class="active">👤 Profile</a>
        <a href="check.php">📩 Check Payment</a>
        <a href="m_report.php">📊 Maintenance Reports</a>
        <a href="m_admin.php">View Maintenance</a>
        <a href="main_history.php">🛠️Maintenance History</a>
        <a href="loginpage.php">⬅️ Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>🔧Set Maintenance</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <!-- Maintenance Form -->
        <form method="POST" action="t_profile.php">
            <div class="maintenance-info">
                <!-- Choose Payment Duration -->
                <div class="form-group-horizontal">
                    <div class="form-item">
                        <label for="payment-duration">Choose Payment Duration:</label>
                        <div class="checkbox-group">
                            <input type="checkbox" name="payment-duration[]" value="1-month" id="1-month">
                            <label for="1-month">1 Month</label>
                            <input type="checkbox" name="payment-duration[]" value="3-months" id="3-months">
                            <label for="3-months">3 Months</label>
                            <input type="checkbox" name="payment-duration[]" value="6-months" id="6-months">
                            <label for="6-months">6 Months</label>
                            <input type="checkbox" name="payment-duration[]" value="1-year" id="1-year">
                            <label for="1-year">1 Year</label>
                        </div>
                    </div>
                </div>
                <div class="form-group-horizontal">
                <div class="form-item">
                    <label for="maintenance-amount">Maintenance Amount:</label>
                    <input type="number" id="maintenance-amount" name="maintenance-amount" required placeholder="Enter Maintenance Amount" /><br>
                    <span id="amount-error" style="color: red; display: none; margin-top: 5px;">Please enter a positive value.</span>
                </div>
                </div>
                <div class="form-group-horizontal">
                    <div class="form-item">
                        <label for="s_date">Start Date:</label>
                        <input type="date" id="s_date" name="s_date" required>
                        <label for="e_date">End Date:</label>
                        <input type="date" id="e_date" name="e_date" readonly>
                    </div>
                </div>

                <button type="submit">Submit Payment</button>
            </div>
        </form>
        
        <!-- Previous Button -->
        <a href="m_admin.php"><button id="prev-button" class="prev-button">Previous</button></a>
    </div>

    <script>
    // Handle changes in payment duration to enforce only one selection
    document.querySelectorAll('input[name="payment-duration[]"]').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            // If the checkbox is checked, uncheck all others
            if (this.checked) {
                document.querySelectorAll('input[name="payment-duration[]"]').forEach(function (otherCheckbox) {
                    // Uncheck all other checkboxes except the one clicked
                    if (otherCheckbox !== checkbox) {
                        otherCheckbox.checked = false;
                    }
                });

                // Optionally: If a duration is selected, reset the start date (require the user to re-select the start date)
                document.getElementById('s_date').value = '';
            }
        });
    });

    // Auto-update the end date based on start date and payment duration
    document.getElementById('s_date').addEventListener('change', function () {
        var startDate = new Date(this.value);
        var selectedDuration = document.querySelector('input[name="payment-duration[]"]:checked');

        if (selectedDuration) {
            var monthsToAdd = {
                '1-month': 1,
                '3-months': 3,
                '6-months': 6,
                '1-year': 12
            };

            var duration = monthsToAdd[selectedDuration.value];
            if (duration) {
                startDate.setMonth(startDate.getMonth() + duration);
                var endDate = startDate.toISOString().split('T')[0];
                document.getElementById('e_date').value = endDate;
            }
        }
    });
    document.querySelector('form').addEventListener('submit', function(event) {
    var maintenanceAmount = document.getElementById('maintenance-amount').value;
    var errorMessage = document.getElementById('amount-error');

    // Validate the Maintenance Amount field
    if (maintenanceAmount <= 0 || maintenanceAmount === "") {
        errorMessage.style.display = 'inline';  // Show error message
        event.preventDefault();  // Prevent form submission
    } else {
        errorMessage.style.display = 'none';  // Hide error message if valid
    }
});

    </script>
</body>
</html>
