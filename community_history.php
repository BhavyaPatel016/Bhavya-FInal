<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "project1"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default sorting order is descending (latest first)
$sortOrder = 'DESC'; // Default to 'DESC' if no sorting option is selected

// If sorting order is specified in the URL, change the query order
if (isset($_GET['sort']) && $_GET['sort'] == 'asc') {
    $sortOrder = 'ASC'; // Ascending order
} elseif (isset($_GET['sort']) && $_GET['sort'] == 'desc') {
    $sortOrder = 'DESC'; // Descending order
}

// SQL query to fetch all users from the commity table, ordering by YEAR and MONTH of created_at
$sql = "SELECT * FROM commity ORDER BY YEAR(created_at) $sortOrder, MONTH(created_at) $sortOrder";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Committee History Page</title>
    <style>
        /* Optional styling for your table */
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
            padding: 9.5px;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            margin-top:25px;
        }
        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s ease;
        }
        .sidebar a:hover {
            background: #5a7a87;
            transform: scale(1.05);
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
            font-size: 24px;
        }
        .header .logout {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .header .logout:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2 style="font-size:20px;">👥 Committee History Dashboard</h2>
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
        <h1>Committee History</h1>
        <a href="loginpage.php"><button class="logout">Logout</button></a>
    </div>

    <h3></h3>
    <form method="get">
        <label for="sort">Sort by Dates:</label>
        <select name="sort" onchange="this.form.submit()">
            <option value="desc" <?php if ($sortOrder == 'DESC') echo 'selected'; ?>>Newest First</option>
            <option value="asc" <?php if ($sortOrder == 'ASC') echo 'selected'; ?>>Oldest First</option>
        </select>
    </form>

    <h3></h3>
    
    <?php if (isset($errorMessage)) echo "<p style='color:red;'>$errorMessage</p>"; ?>
    <?php if (isset($successMessage)) echo "<p style='color:green;'>$successMessage</p>"; ?>

    <table>
        <thead>
            <tr>
                <th>Flat</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    $createdAt = new DateTime($user['created_at']);
                    $createdAtFormatted = $createdAt->format('d-m-Y H:i:s'); // Format to Month Year
                    
                    echo "<tr>
                            <td>" . htmlspecialchars($user['flat']) . "</td>
                            <td>" . htmlspecialchars($user['name']) . "</td>
                            <td>" . htmlspecialchars($user['email']) . "</td>
                            <td>" . $createdAtFormatted . "</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>




<?php
// Database connection settings
/*$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "project1"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default query
$sql = "SELECT * FROM commity";

// Check if user selected a start date and end date for filtering
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $sql .= " WHERE created_at BETWEEN '$start_date' AND '$end_date'";
}

// Add sorting by created_at date
$sortOrder = 'DESC'; // Default to 'DESC' if no sorting option is selected
if (isset($_GET['sort']) && $_GET['sort'] == 'asc') {
    $sortOrder = 'ASC'; // Ascending order
} elseif (isset($_GET['sort']) && $_GET['sort'] == 'desc') {
    $sortOrder = 'DESC'; // Descending order
}

$sql .= " ORDER BY created_at $sortOrder";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Committee History Page</title>
    <style>
        /* Optional styling for your table 
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
            padding: 9.5px;
            position: fixed;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            margin-top:25px;
        }
        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s ease;
        }
        .sidebar a:hover {
            background: #5a7a87;
            transform: scale(1.05);
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
            font-size: 24px;
        }
        .header .logout {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .header .logout:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h2 style="font-size:20px;">👥 Committee History Dashboard</h2>
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
        <h1>Committee History</h1>
        <a href="loginpage.php"><button class="logout">Logout</button></a>
    </div>

    <h3>Filter Committee History By Date Range</h3>
    <form method="get">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" required>

        <button type="submit">Filter</button>
    </form>

    <h3></h3>
    
    <?php if (isset($errorMessage)) echo "<p style='color:red;'>$errorMessage</p>"; ?>
    <?php if (isset($successMessage)) echo "<p style='color:green;'>$successMessage</p>"; ?>

    <table>
        <thead>
            <tr>
                <th>Flat</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    $createdAt = new DateTime($user['created_at']);
                    $createdAtFormatted = $createdAt->format('d-m-Y H:i:s');
                    
                    echo "<tr>
                            <td>" . htmlspecialchars($user['flat']) . "</td>
                            <td>" . htmlspecialchars($user['name']) . "</td>
                            <td>" . htmlspecialchars($user['email']) . "</td>
                            <td>" . $createdAtFormatted . "</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>*/