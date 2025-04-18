<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['email']) && !empty($_GET['email'])) {
    $email = $_GET['email'];

    $query = "SELECT * FROM userlogin1 WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "No user found with the provided email!";
        exit();
    }
} else {
    echo "Email parameter is missing or invalid!";
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@300;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            height: 100vh;
            background-color: #f4f4f9; /* Light off-white background */
            color: #333333; /* Dark grey text */
            overflow: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 60px;
            background-color: #2C3E50; /* Dark background */
            color: white;
            padding: 30px 20px;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar:hover {
            width: 275px;
        }

        .sidebar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            text-align: center;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }

        .sidebar-menu {
            list-style-type: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            margin-top: 20px;
        }

        .sidebar:hover .sidebar-menu {
            opacity: 1;
        }

        .sidebar-menu li {
            margin: 15px 0;
        }

        .sidebar-menu li a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            display: block;
            padding: 12px;
            border-radius: 5px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        .sidebar-menu li a:hover {
            background-color: #007BFF; /* Blue accent on hover */
            padding-left: 20px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 60px;
            padding: 20px;
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 275px;
        }

        /* Top Navigation Bar */
        .top-nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background-color: white;
            padding: 12px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            font-weight: bold;
            font-size: 22px;
            color: #34495E; /* Slightly lighter text */
        }

        /* Profile Card */
        .profile-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-card h3 {
            font-size: 28px;
            color: #2C3E50; /* Darker text for titles */
            margin-bottom: 10px;
        }

        .profile-card p {
            font-size: 20px;
            color: #777777; /* Light grey for general text */
            margin: 10px 0;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .card {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 30%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card h3 {
            font-size: 22px;
            color: #2C3E50; /* Darker text for titles */
        }

        .card p {
            font-size: 36px;
            color: #007BFF; /* Blue accent for card values */
        }

        .card small {
            display: block;
            margin-top: 5px;
            color: #777777; /* Light grey text */
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        /* Footer */
        footer {
            text-align: center;
            background-color: #2C3E50; /* Dark background */
            color: white;
            padding: 15px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .dashboard-cards {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 80%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul class="sidebar-menu">
            <h2 style="text-align: center;"><?php echo htmlspecialchars($user['name']); ?> DashBoard</h2>
            <li><a href="rental_profile.php?email=<?php echo urlencode($userEmail); ?>">👤 Profile</a></li>
            <li><a href="#">📈 C</a></li>
            <li><a href="rental_main.php?email=<?php echo urlencode($user['email']); ?>">💸 Payment</a></li>
            <li><a href="#">💬 E</a></li>
            <li><a href="loginpage.php">⬅️ Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation Bar -->
        <div class="top-nav">
            <div class="user-info">
                <span><?php echo htmlspecialchars($user['name']); ?></span>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="profile-card">
            <h3>Profile</h3>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Phone Number: <?php echo htmlspecialchars($user['number']); ?></p>
            <p>Aadha Number: <?php echo htmlspecialchars($user['acno']); ?></p>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <h3>Flat</h3>
                <p><?php echo htmlspecialchars($user['flat']); ?></p>
            </div>
            <div class="card">
                <h3>Role</h3>
                <p><?php echo htmlspecialchars($user['who']); ?></p>
            </div>
            <div class="card">
                <h3>Rental Date</h3>
                <p><?php echo date('d-m-Y', strtotime($user['rentalDate'])); ?></p>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2024 Project Dashboard. All Rights Reserved.</p>
        </footer>
    </div>

</body>
</html>
