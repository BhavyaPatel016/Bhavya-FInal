<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error_message = "";
$success_message = "";

// Ensure email and flat are passed in query string
if (!isset($_GET['email']) || !isset($_GET['flat'])) {
    die("Unauthorized access");
}

$email = $_GET['email'];  // Get email from query parameters
$flat_number = $_GET['flat'];  // Get flat number from query parameters

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "project1"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate that passwords match
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $error_message = "Password must be at least 6 characters long!";
    } else {
        // Hash the password before updating in the database
        $hashed_password = ($new_password);

        // Prepare SQL query to update password
        $stmt = $conn->prepare("UPDATE userlogin1 SET password = ? WHERE email = ? AND flat = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind the parameters correctly
        $stmt->bind_param("ssi", $hashed_password, $email, $flat_number);
        
        // Execute the query
        if ($stmt->execute()) {
            $success_message = "Password changed successfully!";
            header("Location: loginpage.php");
        } else {
            $error_message = "Failed to update the password. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: url('./image1.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            height: 100vh;
            padding-left: 200px;
            color: #333;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 55px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 299px;
            text-align: left;
        }
        h1 {
            margin-bottom: 20px;
            color: #4CAF50;
            text-align: center;
        }
        input {
            width: calc(100% - 24px);
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #4CAF50;
            outline: none;
        }
        .buttons-container {
            display: flex;
            justify-content: space-between;
        }
        button {
            width: 70%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 30px;
            background-color: green;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 15px;
        }
        button:hover {
            background-color: #4CAF50;
            transform: scale(1.05);
        }
        .back-button {
            background-color: green;
            width:100%;
            margin-right:20px;
        }
        .back-button:hover {
            background-color: #4CAF50;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .success-message {
            color: green;
            text-align: center;
            margin-top: 10px;
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            text-decoration: none;
            color: #4CAF50;
            margin: 0 10px;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Change Password</h1>
        <form action="" method="POST">
            <input type="password" name="new_password" placeholder="Enter New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            
            <div class="buttons-container">
                <button type="submit">Change Password</button>
                <a href="forget_password.php">
                    <button type="button" class="back-button">Back</button>
                </a>
            </div>
        </form>
        <?php if (!empty($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php } ?>
    </div>
</body>
</html>
