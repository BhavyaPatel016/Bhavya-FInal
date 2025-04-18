<?php
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

// Query to fetch only unique records based on flat_no, start_date, end_date
$sql = "
    SELECT MIN(id) as id, flat_no, name, phone, email, payment_duration, start_date, end_date, amount, payment_method 
    FROM maintenance1
    GROUP BY flat_no, start_date, end_date
    ORDER BY start_date DESC
";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
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
        <h2>📊 Maintenance Report</h2>
        <a href="t_profile.php">👤 Profile</a>
        <a href="check.php">📩 Check Payment</a>
        <a href="m_report.php" class="active">📊 Maintenance Reports</a>
        <a href="onetimepayment.php">💰One Time Payment</a>
        <a href="onetime_report.php">💰One Time Report</a>
        <a href="main_history.php">🛠️Maintenance History</a>
        <a href="loginpage.php">⬅️ Logout</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>📊 Maintenance Report</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Flat Number <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(0, this.value)" placeholder="Search Flat Number..."></th>
                    <th>Owner Name <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(1, this.value)" placeholder="Search Owner Name..."></th>
                    <th>Phone Number <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(2, this.value)" placeholder="Search Phone..."></th>
                    <th>Email <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(3, this.value)" placeholder="Search Email..."></th>
                    <th>Duration <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(4, this.value)" placeholder="Search Duration..."></th>
                    <th>Start Date <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(5, this.value)" placeholder="Search Start Date..."></th>
                    <th>End Date <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(6, this.value)" placeholder="Search End Date..."></th>
                    <th>Amount <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(7, this.value)" placeholder="Search Amount..."></th>
                    <th>Method <span class="search-icon" onclick="toggleSearch(this)">🔍</span><input type="text" class="search-bar" onkeyup="filterTable(8, this.value)" placeholder="Search Method..."></th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['flat_no']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_duration']) . "</td>";
                        echo "<td>" . date("d-m-Y", strtotime($row['start_date'])) . "</td>";
                        echo "<td>" . date("d-m-Y", strtotime($row['end_date'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='no-record'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleSearch(icon) {
            const searchBar = icon.nextElementSibling;
            searchBar.style.display = (searchBar.style.display === 'block') ? 'none' : 'block';
        }

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
