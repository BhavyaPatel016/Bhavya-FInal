<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$database = "project1";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize $row as null
$row = null;

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer to prevent SQL injection

    // Fetch existing record
    $sql = "SELECT * FROM onepayment WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<script>
                alert('Record not found');
                window.location.href = 'm_admin.php';
              </script>";
        exit();
    }
}

// Handle form submission for updating the record
if ($_SERVER["REQUEST_METHOD"] == "POST" && $row != null) {
    $payment_reason = mysqli_real_escape_string($conn, $_POST['payment_reason']);
    $payment_date = mysqli_real_escape_string($conn, $_POST['payment_date']);
    $one_time_amount = mysqli_real_escape_string($conn, $_POST['one_time_amount']);

    // Update query
    $update_sql = "UPDATE onepayment 
                   SET payment_reason = '$payment_reason', 
                       payment_date = '$payment_date', 
                       one_time_amount = '$one_time_amount' 
                   WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        // Set session variables for the updated record
        $_SESSION['payment_reason'] = $payment_reason;
        $_SESSION['payment_date'] = $payment_date;
        $_SESSION['one_time_amount'] = $one_time_amount;

        // Redirect and display success message
        echo "<script>
                alert('Record updated successfully');
                window.location.href = 'm_admin.php';
              </script>";
    } else {
        echo "<script>
                alert('Error updating record: " . mysqli_error($conn) . "');
              </script>";
    }
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit One-Time Payment</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f4f7f6, #ffffff);
            color: #333;
        }

        .header {
            background: #3498db;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            margin-bottom: 40px;
        }

        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 0 20px;
        }

        .form-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .form-container h2 {
            text-align: center;
            color: #3498db;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .form-container label {
            font-weight: bold;
            display: block;
            color: #555;
            margin-bottom: 8px;
        }

        .form-container input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        .form-container input:focus {
            outline: none;
            border-color: #3498db;
            background-color: #ffffff;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .form-container button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            border: none;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
            transition: 0.3s ease;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 16px;
            color: #3498db;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Edit One-Time Payment</h1>
    </div>

    <div class="main-content">
        <div class="form-container">
            <h2>Edit Payment Details</h2>

            <?php if ($row !== null) : ?>
                <form method="POST">
                    <label for="payment_reason">Payment Reason:</label>
                    <input type="text" name="payment_reason" value="<?php echo htmlspecialchars($row['payment_reason']); ?>" required>

                    <label for="payment_date">Payment Date:</label>
                    <input type="date" name="payment_date" value="<?php echo $row['payment_date']; ?>" required>

                    <label for="one_time_amount">Payment Amount:</label>
                    <input type="number" step="0.01" name="one_time_amount" value="<?php echo $row['one_time_amount']; ?>" required>

                    <button type="submit">Update Payment</button>
                </form>
            <?php else : ?>
                <p>Record not found. Please check the URL or try again later.</p>
            <?php endif; ?>

            <a href="m_admin.php" class="back-link">Cancel</a>
        </div>
    </div>
</body>
</html>
