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

// SQL query to fetch data from admin_maintenance table
$sql = "SELECT * FROM admin_maintenance"; // Change table name here
$result = mysqli_query($conn, $sql);
$sql_onepayment = "SELECT * FROM onepayment"; 
$result_onepayment = mysqli_query($conn, $sql_onepayment);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Maintenance </title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #eef2f3, #e3f2fd); /* Light gradient background */
            color: #333;
            display: flex;
            transition: background 0.3s ease;
        }

        /* Sidebar Styles */
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
            position: relative; /* Needed for absolute positioning of the button */
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

        /* Add Button Styles (outside the table, right corner) */
        .add-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 15px;
            background-color: #3498db;
            color: white;
            font-size: 24px;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .add-button:hover {
            background-color: #2980b9;
        }
        /* Add Payment button for One Time Payment Details section */
        .add-payment-button {
            padding: 12px 20px;
            background-color: #27ae60;
            padding: 8px 12px;
            margin: 5px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            transition: 0.3s ease;
        }

        .add-payment-button:hover {
            background-color: #2ecc71;
        }
        .action-button.edit {
            background-color: #f39c12; /* Orange for Edit */
            text-decoration:none;
        }
        .action-button {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            transition: 0.3s ease;
        }
        .action-button.delete {
    background-color: #E74C3C; /* Red for Delete */
    text-decoration: none;
}

.action-button.delete:hover {
    background-color: #C0392B;
}

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🛠️Maintenance History</h2>
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
            <h1>View Maintenance</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Payment Duration</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Maintenance Amount</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $formatted_start_date = date("d-m-Y", strtotime($row['start_date']));
                        $formatted_end_date = date("d-m-Y", strtotime($row['end_date']));

                        echo "<tr>";
                        echo "<td>" . $row['payment_duration'] . "</td>";
                        echo "<td>" . $formatted_start_date . "</td>";
                        echo "<td>" . $formatted_end_date . "</td>";
                        echo "<td>" . $row['maintenance_amount'] . "</td>";
                        echo "<td>" . $row['total_amount'] . "</td>";
                        echo "<td>";
echo "<a href='main_edit.php?id=" . $row['id'] . "' class='action-button edit'>Edit</a> ";
echo "<a href='delete_maintenance.php?id=" . $row['id'] . "' class='action-button delete' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>";
echo "</td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="header">
            <h1>Check One Time Payment Details</h1>
            <div class="add-payment-button">
            <a href="m_o_payment.php" style="text-decoration:none;color:white;">Add Payment</a>
        </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Payment Reason</th>
                    <th>Payment Date</th>
                    <th>Payment Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
    <?php
    if (mysqli_num_rows($result_onepayment) > 0) {
        while ($row = mysqli_fetch_assoc($result_onepayment)) {
            $pd = date("d-m-Y", strtotime($row['payment_date']));
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['payment_reason']) . "</td>";
            echo "<td>" . $pd . "</td>";
            echo "<td>" . htmlspecialchars($row['one_time_amount']) . "</td>";
            echo "<td>";
            echo "<a href='onepayment_edit.php?id=" . $row['id'] . "' class='action-button edit'>Edit</a> ";
            echo "<a href='delete_onepayment.php?id=" . $row['id'] . "' class='action-button delete' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' style='text-align:center;'>No records found</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>
</body>
</html>
