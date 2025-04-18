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
$sql = "SELECT * FROM admin_maintenance1"; // Change table name here
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance History</title>
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

        /* No data found */
        .no-record {
            text-align: center;
            color: #666;
            font-size: 16px;
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
            <h1>🛠️ Maintenance History</h1>
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
                </tr>
            </thead>
            <tbody id="table-body">
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['payment_duration'] . "</td>";
                                echo "<td>" . $row['start_date'] . "</td>";
                                echo "<td>" . $row['end_date'] . "</td>";
                                echo "<td>" . $row['maintenance_amount'] . "</td>";
                                echo "<td>" . $row['total_amount'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='no-record'>No records found</td></tr>";
                        }
                    ?>
            </tbody>
        </table>
    </div>
</body>
</html>