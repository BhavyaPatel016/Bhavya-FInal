<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "project1";

// Create a connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Insert new record
if (isset($_POST['add_payment'])) {
    $payment_reason = $_POST['payment_reason'];
    $payment_date = $_POST['payment_date'];
    $one_time_amount = $_POST['one_time_amount'];

    // Validation for positive value
    if ($one_time_amount <= 0) {
        echo "Please enter a positive value for the One-Time Amount.";
        exit();
    }

    $insert_sql = "INSERT INTO onepayment (payment_reason, payment_date, one_time_amount) 
                   VALUES ('$payment_reason', '$payment_date', '$one_time_amount')";

    if (mysqli_query($conn, $insert_sql)) {
        echo "New record added successfully.";
        header("Location: m_admin.php"); // Redirect to the maintenance history page
    } else {
        echo "Error inserting record: " . mysqli_error($conn);
    }
}

// Update existing record
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM admin_maintenance WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "No record found.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_payment'])) {
    $payment_reason = $_POST['payment_reason'];
    $payment_date = $_POST['payment_date'];
    $one_time_amount = $_POST['one_time_amount'];

    // Validation for positive value
    if ($one_time_amount <= 0) {
        echo "Please enter a positive value for the One-Time Amount.";
        exit();
    }

    $update_sql = "UPDATE admin_maintenance 
                   SET payment_reason = '$payment_reason', payment_date = '$payment_date', 
                       one_time_amount = '$one_time_amount' 
                   WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        echo "Record updated successfully.";
        header("Location: m_admin.php");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Payment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00b894, #0984e3);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
            overflow: hidden;
        }

        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .form-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.05);
            z-index: -1;
        }

        .form-container h2 {
            font-size: 32px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
            color: #2d3436;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-container label {
            font-size: 16px;
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
            color: #2d3436;
        }

        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #555;
            transition: all 0.3s ease;
        }

        .form-container input:focus,
        .form-container textarea:focus {
            border-color: #00b894;
            outline: none;
            box-shadow: 0 0 15px rgba(0, 180, 148, 0.3);
        }

        .form-container button {
            width: 100%;
            padding: 16px;
            margin-top: 20px;
            background-color: #0984e3;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        .form-container button:hover {
            background-color: #74b9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(52, 152, 219, 0.2);
        }

        .form-container button:active {
            transform: translateY(1px);
            background-color: #0984e3;
        }

        .form-container button:disabled {
            background-color: #dfe6e9;
            cursor: not-allowed;
        }

        .form-container textarea {
            resize: vertical;
            height: 100px;
        }

        .form-container .section-title {
            margin-top: 40px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            color: #0984e3;
            letter-spacing: 1px;
        }

        /* Validation error message */
        .error {
            color: #e74c3c;  /* Red color */
            font-size: 14px;
            margin-top: 5px;
        }

        /* Subtle animations */
        .form-container input,
        .form-container textarea,
        .form-container button {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.5s forwards;
        }

        .form-container input:nth-child(2),
        .form-container textarea:nth-child(3),
        .form-container button:nth-child(4) {
            animation-delay: 0.3s;
        }

        .form-container input:nth-child(4),
        .form-container button:nth-child(5) {
            animation-delay: 0.6s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
                width: 100%;
            }

            .form-container h2 {
                font-size: 28px;
            }

            .form-container button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Maintenance Payment</h2>
        <form method="POST" id="payment-form">
            <label for="payment_reason">Reason for Payment:</label>
            <textarea name="payment_reason" id="payment_reason" required><?php echo isset($row) ? $row['payment_reason'] : ''; ?></textarea>

            <label for="payment_date">Payment Date:</label>
            <input type="date" name="payment_date" id="payment_date" value="<?php echo isset($row) ? $row['payment_date'] : ''; ?>" required>

            <label for="one_time_amount">One-Time Payment Amount:</label>
            <input type="text" name="one_time_amount" id="one_time_amount" 
                value="<?php echo isset($row) ? $row['one_time_amount'] : ''; ?>" 
                required pattern="^[0-9]+$" title="Please enter a valid whole number" oninput="validateAmount()">
            <div id="amount-error" class="error" style="display:none;">Please enter a positive whole number for the payment amount.</div>

            <?php if (isset($row)) { ?>
                <button type="submit" name="update_payment">Update Record</button>
            <?php } else { ?>
                <button type="submit" name="add_payment">Add Payment</button>
            <?php } ?>
        </form>
        <div class="section-title">One Time Payment Details</div>
    </div>

    <script>
        // Validation function for numeric input
        function validateAmount() {
            var amountField = document.getElementById('one_time_amount');
            var errorMessage = document.getElementById('amount-error');
            var regex = /^[0-9]+$/;  // Only whole numbers allowed

            // Check if the value matches the regex for valid whole number input
            if (!regex.test(amountField.value)) {
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        }

        // Prevent form submission if invalid value is entered
        document.getElementById('payment-form').addEventListener('submit', function(event) {
            var amountField = document.getElementById('one_time_amount');
            var errorMessage = document.getElementById('amount-error');
            
            // Check if the value is a positive whole number
            if (parseInt(amountField.value) <= 0 || isNaN(amountField.value)) {
                errorMessage.style.display = 'block';
                event.preventDefault();  // Prevent form submission
            } else {
                errorMessage.style.display = 'none';
            }
        });
    </script>
</body>
</html>
