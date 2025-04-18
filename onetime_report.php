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

// SQL query to fetch data from userlogin1 table
$sql = "SELECT DISTINCT flat_no, name, phone, email, payment_reason, payment_date, one_time_amount FROM onetimepayment1";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Report</title>
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

        /* Hide search bars by default */
        .search-bar {
            display: none;
            padding: 8px;
            margin-top: 5px;
            width: 100%;
            box-sizing: border-box;
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
        <h2>💰One Time Report</h2>
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
            <h1>💰One Time Report</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        Flat Number
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(0, this.value)" placeholder="Search Flat Number...">
                    </th>
                    <th>
                        Owner Name
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(1, this.value)" placeholder="Search Owner Name...">
                    </th>
                    <th>
                        Phone Number
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(3, this.value)" placeholder="Search Phone Number...">
                    </th>
                    <th>
                        Email
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(2, this.value)" placeholder="Search Owner Email...">
                    </th>
                    <th>
                        Payment Reason
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(4, this.value)" >
                    </th>
                    <th>
                        Payment Date
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(5, this.value)" >
                    </th>
                    <th>
                        One Time Amount
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(6, this.value)">
                    </th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                $seen = array();
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Create a unique key based on all relevant columns
                        $key = $row['flat_no'] . '|' . $row['name'] . '|' . $row['phone'] . '|' . $row['email'] . '|' . $row['payment_reason'] . '|' . $row['payment_date'] . '|' . $row['one_time_amount'];

                        // Only display if not already seen
                        if (!in_array($key, $seen)) {
                            $seen[] = $key;
                            echo "<tr>";
                            echo "<td>" . $row['flat_no'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['phone'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td>" . $row['payment_reason'] . "</td>";
                            echo "<td>" . date("d-m-Y", strtotime($row['payment_date'])) . "</td>";
                            echo "<td>" . $row['one_time_amount'] . "</td>";
                            echo "</tr>";
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Function to toggle search bar visibility
        function toggleSearch(icon) {
            const th = icon.parentElement;
            const searchBar = th.querySelector('.search-bar');
            const isActive = searchBar.style.display === 'block';

            // Toggle visibility of the search bar
            searchBar.style.display = isActive ? 'none' : 'block';
        }

        // Function to filter the table based on user input
        function filterTable(column, value) {
            const rows = document.querySelectorAll("#table-body tr");
            rows.forEach(row => {
                const cell = row.querySelectorAll("td")[column];
                if (cell && cell.textContent.toLowerCase().includes(value.toLowerCase())) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>