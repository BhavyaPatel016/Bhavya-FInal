<?php
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "project1"; // Your database name

$pendingUsers = []; // Initialize the variable to prevent undefined variable warning

try {
    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Fetch pending users from the onetimepayment table, excluding 'rejected' status
    $fetchPendingUsersSql = "SELECT * FROM onetimepayment WHERE status != 'received' AND status != 'rejected'";
    $result = $conn->query($fetchPendingUsersSql);

    if ($result && $result->num_rows > 0) {
        // Fetch all the pending users
        while ($row = $result->fetch_assoc()) {
            $pendingUsers[] = $row; // Add each record to the pendingUsers array
        }
    }

    // Handle "Received" action
    if (isset($_GET['received'])) {
        $id = intval($_GET['received']); // Sanitize ID

        // Prepare the SQL query to fetch the record from the onetimepayment table
        $stmt = $conn->prepare("SELECT * FROM onetimepayment WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $record = $result->fetch_assoc();

            // Insert the record into the onetimepayment1 table
            $insertSql = "INSERT INTO onetimepayment1 (flat_no, name, phone, email, payment_reason, payment_date, one_time_amount)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertSql);
            if (!$stmtInsert) {
                throw new Exception("Error preparing insert statement: " . $conn->error);
            }

            $stmtInsert->bind_param(
                "sssssss", // The data types for each value
                $record['flat_no'], 
                $record['name'], 
                $record['phone'], 
                $record['email'], 
                $record['payment_reason'], 
                $record['payment_date'], 
                $record['one_time_amount']
            );

            // Execute the insert query
            if ($stmtInsert->execute()) {
                // Update the status to "received" in the onetimepayment table
                $updateStmt = $conn->prepare("UPDATE onetimepayment SET status = 'received' WHERE id = ?");
                if (!$updateStmt) {
                    throw new Exception("Error preparing update statement: " . $conn->error);
                }
                $updateStmt->bind_param("i", $id);
                $updateStmt->execute();

                header("Location: onetimepayment.php?status=received");
                exit;
            } else {
                throw new Exception("Error executing insert query into onetimepayment1: " . $stmtInsert->error);
            }
        } else {
            throw new Exception("Record not found in onetimepayment table.");
        }
    }

    // Handle "Unreceived" action
    if (isset($_GET['unreceived'])) {
        $id = intval($_GET['unreceived']); // Sanitize ID

        // Update the status of the record in the onetimepayment table
        $updateSql = "UPDATE onetimepayment SET status = 'rejected' WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        if (!$stmtUpdate) {
            throw new Exception("Error preparing update statement: " . $conn->error);
        }

        $stmtUpdate->bind_param("i", $id);

        if ($stmtUpdate->execute()) {
            header("Location: onetimepayment.php?status=unreceived");
            exit;
        } else {
            throw new Exception("Error updating status: " . $conn->error);
        }
    }

} catch (Exception $e) {
    // Catch exceptions and display the error message
    echo "Error: " . $e->getMessage();
    // You could also log the error to a file if necessary
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>One time Payment Notification</title>
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
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .approve-button {
            background-color: #28a745;
        }

        .approve-button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .reject-button {
            background-color: #dc3545;
        }

        .reject-button:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<div class="sidebar">
        <h2>🛠️One Time Payment</h2>
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
        <h1>💰One Time Payment</h1>
        <button class="logout">Logout</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Flat No</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Reason</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pendingUsers)): ?>
                <?php foreach ($pendingUsers as $user): ?>
                    <tr>
                        <td><?php echo $user['flat_no']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['phone']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['payment_reason']; ?></td>
                        <td><?php echo '₹' . number_format($user['one_time_amount'], 2); ?></td>
                        <td>
                            <a href="?received=<?php echo $user['id']; ?>" class="approve-button">Received</a>
                            <a href="?unreceived=<?php echo $user['id']; ?>" class="reject-button">Unreceived</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No pending payments</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
