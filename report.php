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
$sql = "SELECT name, acno, email, flat, who FROM userlogin1 ORDER BY flat ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 05px;
            background-color: #eef2f3;
            color: #333;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #6793AC;
            color: white;
            height: 100vh;
            padding: 10px;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background: #5a7a87;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6793AC;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
        }
        .header h1 {
            margin: 0;
        }
        .header .logout {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
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
            position: relative;
        }
        th .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-40%);
            cursor: pointer;
        }
        th .search-bar {
            display: none;
            margin-top: 5px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 70%;
        }
        th.active .search-bar {
            display: block;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>📊Report Dashboard</h2>
        <a href="residency_details.php">👤Profile</a>
        <a href="message.php">📩Messages</a>
        <a href="report.php">🏠Resident</a>
        <a href="#">🔧Maintenances</a>
        <a href="#">🗝️Aminities Booking</a>
        <a href="selectcommitymember.php">👥Create Community</a>
        <a href="community_history.php">📜 Community History</a>    
        <a href="loginpage.php">⬅️Logout</a>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>📊Report Dashboard</h1>
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
                        Email
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(3, this.value)" placeholder="Search Email...">
                    </th>
                    <th>
                        Owner/Rental
                        <span class="search-icon" onclick="toggleSearch(this)">🔍</span>
                        <input type="text" class="search-bar" onkeyup="filterTable(4, this.value)" placeholder="Search Owner/Rental...">
                    </th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['flat']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['who']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleSearch(icon) {
            const th = icon.parentElement;
            const searchBar = th.querySelector('.search-bar');
            const isActive = th.classList.contains('active');

            // Close other active search bars
            document.querySelectorAll('th.active').forEach(activeTh => {
                activeTh.classList.remove('active');
                activeTh.querySelector('.search-bar').style.display = 'none';
            });

            // Toggle current search bar
            if (!isActive) {
                th.classList.add('active');
                searchBar.style.display = 'block';
                searchBar.focus();
            } else {
                th.classList.remove('active');
                searchBar.style.display = 'none';
            }
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

<?php
mysqli_close($conn);
?>