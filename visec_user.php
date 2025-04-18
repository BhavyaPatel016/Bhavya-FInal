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

    // Fetch committee data
    $committeeQuery = "SELECT * FROM visesecretory";
    $committeeResult = $conn->query($committeeQuery);

    // Check if the query was successful
    if ($committeeResult === false) {
        die("Error executing query: " . $conn->error);
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
    <title>Vise-secretory View Page</title>
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
            background-color: #f4f4f9;
            color: #333333;
            overflow: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 60px;
            background-color: #2C3E50;
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
            background-color: #007BFF;
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
            color: #34495E;
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
            color: #2C3E50;
            margin-bottom: 10px;
        }

        .profile-card p {
            font-size: 20px;
            color: #777777;
            margin: 10px 0;
        }

        /* Committee Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    /* Button Styling */
        .buttons {
            margin-bottom: 30px;
            margin-left: 02px;
        }

        .btn {
            background-color: #28a745; /* Green color */
            color: white;
            padding: 10px 12px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #45a049;  
        }
        /* Footer */
        footer {
            text-align: center;
            background-color: #2C3E50;
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
            <li><a href="u_profile.php?email=<?php echo urlencode($user['email']); ?>">👤 Profile</a></li>
            <li><a href="onboarding.php?email=<?php echo urlencode($user['email']); ?>">📝 Onboarding</a></li>
            <li><a href="#">📈 Dashboard</a></li>
            <li><a href="maintenance.php?email=<?php echo urlencode($user['email']); ?>">💸 Payment</a></li>
            <li><a href="#">💬 Help</a></li>
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

        <!-- A, B, C Buttons -->
        <div class="buttons">
            <a href="onboarding.php?email=<?php echo urlencode($user['email']); ?>"class="btn">Committee</a>
            <a href="pre_user.php?email=<?php echo urlencode($user['email']); ?>"class="btn">President</a>
            <a href="sec_user.php?email=<?php echo urlencode($user['email']); ?>" class="btn">Secretory</a>
            <a href="visec_user.php?email=<?php echo urlencode($user['email']); ?>" class="btn">Vice-Secretory</a>
        </div>

        <!-- Committee Members Table -->
        <div class="profile-card">
            <h3>Committee Members</h3>
            <table>
                <thead>
                    <tr>
                        <th>Flat Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($committeeResult->num_rows > 0) : ?>
                        <?php while ($committee = $committeeResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($committee['flat']); ?></td>
                                <td><?php echo htmlspecialchars($committee['name']); ?></td>
                                <td><?php echo htmlspecialchars($committee['email']); ?></td>
                                <td><?php echo htmlspecialchars($committee['number']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5">No Vise-Secretory members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 Project Dashboard. All Rights Reserved.</p>
        </footer>
    </div>

</body>
</html>
