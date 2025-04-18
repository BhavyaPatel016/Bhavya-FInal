<?php
session_start(); // Start the session at the very beginning

error_reporting(E_ALL);
ini_set('display_errors', 1);

$admin_email = 'admin123@gmail.com';
$admin_password = 'password123'; 
$t_email ='abc123@gmail.com';
$t_pass = 'bmgk123';

$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "project1"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $input_password = $_POST['password'];

    // Admin login check (separate condition for admin)
    if ($email === $admin_email && $input_password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: residency_details.php');
        exit();
    }

    // Check if the user exists in the database (both regular and rental users)
    $stmt = $conn->prepare("SELECT email, password, who FROM userlogin1 WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the email matches but password is incorrect
        if ($input_password !== $user['password']) {
            $error_message = 'Incorrect password. Please try again.';
        } else {
            $_SESSION['user_logged_in'] = true;

            // Check the 'who' field and redirect accordingly
            if ($user['who'] === 'Rental') {
                // Redirect to rental user profile
                header("Location: rental_profile.php?email=" . urlencode($email));
                exit();
            } elseif ($user['who'] === 'Owner') {
                // Redirect to owner profile
                header("Location: u_profile.php?email=" . urlencode($email));
                exit();
            }
        }
    } else {
        $error_message = 'Invalid email or password.';
    }

    $stmt->close();
}

$conn->close();
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        button {
            padding: 12px;
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 15px;
        }
        .sign-in-button {
            background-color: green; 
            width: 100%;
        }
        .back-button {
            background-color: green;
            width: 90px;
        }
        button:hover {
            transform: scale(1.05);
            background-color: #45a049;
        }
        .error-message {
            color: red; 
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
        <h1>Sign In</h1>
        <form action="loginpage.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required> 
            <div class="button-container">
                <!-- Sign In Button -->
                <button type="submit" class="sign-in-button">Sign In</button>

                <!-- Back Button as a link -->
                <a href="homepage.php"><button type="button" class="back-button">Back</button></a>
            </div>
        </form>
        
        <?php if (!empty($error_message)) { ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php } ?>
        
        <div class="links">
            <a href="forget_password.php">Change Password</a>
            <span>|</span>
            <a href="registerpage.php">Sign Up</a>
        </div>
    </div>
</body>
</html>