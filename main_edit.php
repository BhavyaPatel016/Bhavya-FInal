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

// Check if the ID is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the data for the specific entry
    $sql = "SELECT * FROM admin_maintenance WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "No record found.";
        exit;
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated values from the form
    $payment_duration = $_POST['payment_duration'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $maintenance_amount = $_POST['maintenance_amount'];

    // Calculate total amount
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);
    $interval = $start_date_obj->diff($end_date_obj);
    $months = $interval->m + ($interval->y * 12);

    $total_amount = $months * $maintenance_amount;

    // Update the record in the database
    $update_sql = "UPDATE admin_maintenance 
                   SET payment_duration = '$payment_duration', start_date = '$start_date', 
                       end_date = '$end_date', maintenance_amount = '$maintenance_amount', 
                       total_amount = '$total_amount' 
                   WHERE id = $id";

    if (mysqli_query($conn, $update_sql)) {
        echo "Record updated successfully.";
        header("Location: m_admin.php"); // Redirect to the maintenance history page
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
    <title>Edit Maintenance Record</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
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
        }

        .form-container button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Edit Maintenance Record</h1>
    </div>

    <div class="main-content">
        <div class="form-container">
            <form method="POST">
                <label for="payment_duration">Payment Duration:</label>
                <select name="payment_duration" id="payment_duration" required>
                    <option value="1 month" <?php echo ($row['payment_duration'] == '1 month') ? 'selected' : ''; ?>>1 month</option>
                    <option value="3 months" <?php echo ($row['payment_duration'] == '3 months') ? 'selected' : ''; ?>>3 months</option>
                    <option value="6 months" <?php echo ($row['payment_duration'] == '6 months') ? 'selected' : ''; ?>>6 months</option>
                    <option value="1 year" <?php echo ($row['payment_duration'] == '1 year') ? 'selected' : ''; ?>>1 year</option>
                </select>

                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo $row['start_date']; ?>" required>

                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo $row['end_date']; ?>" readonly required>

                <label for="maintenance_amount">Maintenance Amount (Per Month):</label>
                <input type="number" name="maintenance_amount" id="maintenance_amount" value="<?php echo $row['maintenance_amount']; ?>" required min="0" step="0.01">

                <label for="total_amount">Total Amount:</label>
                <input type="number" name="total_amount" id="total_amount" value="<?php echo $row['total_amount']; ?>" readonly>

                <button type="submit">Update Record</button>
            </form>
        </div>
    </div>

    <script>
        function updateEndDate() {
            var startDate = document.getElementById('start_date').value;
            var paymentDuration = document.getElementById('payment_duration').value;
            var endDateInput = document.getElementById('end_date');

            if (startDate) {
                var startDateObj = new Date(startDate);
                var monthsToAdd = {
                    "1 month": 1,
                    "3 months": 3,
                    "6 months": 6,
                    "1 year": 12
                }[paymentDuration];

                startDateObj.setMonth(startDateObj.getMonth() + monthsToAdd);
                endDateInput.value = startDateObj.toISOString().split('T')[0];
            }

            updateTotalAmount();
        }

        function updateTotalAmount() {
    var maintenanceAmount = parseFloat(document.getElementById('maintenance_amount').value) || 0;
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;

    if (startDate && endDate) {
        var startDateObj = new Date(startDate);
        var endDateObj = new Date(endDate);
        var monthDifference = (endDateObj.getFullYear() - startDateObj.getFullYear()) * 12 + (endDateObj.getMonth() - startDateObj.getMonth());

        if (monthDifference > 0) {
            document.getElementById('total_amount').value = (monthDifference * maintenanceAmount).toFixed(2);
        } else {
            document.getElementById('total_amount').value = '0.00';
        }
    }
}


        document.getElementById('payment_duration').addEventListener('change', updateEndDate);
        document.getElementById('start_date').addEventListener('change', updateEndDate);
        document.getElementById('maintenance_amount').addEventListener('change', updateTotalAmount);

        window.onload = updateEndDate;
    </script>

</body>
</html>
