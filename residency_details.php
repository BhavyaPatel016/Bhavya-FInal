<?php
// database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "project1"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch the data to display in a table after form submission
$sql = "SELECT * FROM residency";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 05px;
            background-color: #eef2f3;
            color: #333;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            background: #6793AC;
            color: white;
            height: 100vh;
            padding: 9.5px;
            position: fixed;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar:hover {
            box-shadow: 6px 0 15px rgba(0, 0, 0, 0.2);  
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            transition: font-size 0.3s ease;
        }

        .sidebar:hover h2 {
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            position: relative;
            transition: background-color 0.3s, transform 0.3s ease;
        }

        .sidebar a:hover {
            background: #5a7a87;
            transform: scale(1.05);
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #ffffff;
            bottom: 0;
            left: 0;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .sidebar a:hover::before {
            transform: scaleX(1);
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
            transition: margin-left 0.3s ease, padding 0.3s ease;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #6793AC;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .header:hover {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
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
            transition: background 0.3s;
        }

        .header .logout:hover {
            background: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
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

        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .form-group input {
            width: 85%;
            padding: 10px;
            font-size: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.7);
            outline: none;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        /* Added CSS for the Previous button */
        .prev-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s ease;
        }

        .prev-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Residency Details</h2>
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
            <h1>Residency Details</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>
        <table>
            <tr>
                <th>Residency Name</th>
                <th>Number of Floors</th>
                <th>Flats per Floor</th>
                <th>Flat Sequence</th>
                <th>Residency Address</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['residency_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['number_of_floors']); ?></td>
                        <td><?php echo htmlspecialchars($row['flats_per_floor']); ?></td>
                        <td><?php echo htmlspecialchars($row['flat_sequence']); ?></td>
                        <td><?php echo htmlspecialchars($row['residency_address']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No records found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Previous button -->
    <a href="adminpage.php">
        <button class="prev-button">Previous</button>
    </a>
</body>
</html>
