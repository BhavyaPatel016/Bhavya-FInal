<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$error_message = ""; // Initialize error message
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $flat = trim($_POST['flat_number']);

    // Validate that flat number contains only digits
    if (!ctype_digit($flat)) {
        $error_message = "Flat number must contain only digits.";
    } else {
        // Prepare SQL query to fetch user details
        $stmt = $conn->prepare("SELECT * FROM userlogin1 WHERE email = ? AND flat = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("si", $email, $flat);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $success_message = "User verified successfully. You can reset your password.";
            // Redirect to the change_password.php page with email and flat in the query string
            header("Location: change_password.php?email=$email&flat=$flat");
            exit(); // Don't forget to exit after redirect to avoid further processing
        } else {
            $error_message = "No matching user found. Please check your details.";
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
    <title>Forget Password</title>
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
            background-color:  #4CAF50;
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
    <script>
        function validateFlatNumber(event) {
            let key = event.key;
            if (!/^\d$/.test(key)) {
                event.preventDefault();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Forget Password</h1>
        <form action="" method="POST">
            <input type="email" name="email" placeholder="Enter your Email" required>
            <input type="text" name="flat_number" placeholder="Enter Flat Number" required onkeypress="validateFlatNumber(event)">
            
            <div class="buttons-container">
                <button type="submit">Submit</button>
                <a href="loginpage.php">
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
