<?php
    session_start(); // Start the session for message handling

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

    // SQL query to fetch all users who are still pending approval (status = 'pending')
    $sql = "SELECT * FROM userlogin WHERE status = 'pending'";  // Only fetch users with 'pending' status
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result === false) {
        die("Error executing query: " . $conn->error);
    }

    // Initialize the pendingUsers array
    $pendingUsers = $result->fetch_all(MYSQLI_ASSOC);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Load Composer's autoloader
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/SMTP.php';
    //SENDER
    function sendNotificationEmail($name, $email, $status) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Set Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'ressiment@gmail.com';  // Your Gmail address
            $mail->Password = 'llyn fmwo nkzj kzpk';  // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Use STARTTLS
            $mail->Port = 587;  // Port 587 for TLS
            $mail->setFrom('ressiment@gmail.com', 'Ressiment');
            $mail->addAddress($email, $name);  // User's email address
            $mail->isHTML(true);
            $mail->Subject = 'Your Registration Status';
            $mail->Body    = "Dear $name,<br><br>Your registration status has been updated to: $status.<br><br>Thank you!";
            $mail->send();
        } catch (Exception $e) {
            $_SESSION['mail_error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Approve user
    if (isset($_GET['approve'])) {
        $userId = $_GET['approve'];

        // Fetch user details from userlogin table
        $stmt = $conn->prepare("SELECT * FROM userlogin WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing the SELECT query: " . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            die("Error executing the SELECT query: " . $stmt->error);
        }
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            // Check if the user already exists in the userlogin1 table
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM userlogin1 WHERE id = ?");
            $checkStmt->bind_param("i", $user['id']);
            $checkStmt->execute();
            $checkStmt->bind_result($exists);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($exists > 0) {
                die("This user has already been approved and exists in the system.");
            }

            // Insert into userlogin1 table
            $insertStmt = $conn->prepare("INSERT INTO userlogin1 (id, name, number, email, acno, image_path, password, flat, floor, who, purchaseDate, rentalDate) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($insertStmt === false) {
                die("Error preparing the INSERT query: " . $conn->error);
            }
            $insertStmt->bind_param("isssssssssss", $user['id'], $user['name'], $user['number'], $user['email'], 
                                    $user['acno'], $user['image_path'], $user['password'], $user['flat'], $user['floor'], 
                                    $user['who'], $user['purchaseDate'], $user['rentalDate']);
            if (!$insertStmt->execute()) {
                die("Error executing the INSERT query: " . $insertStmt->error);
            }

            // Update status to 'approved'
            $updateStmt = $conn->prepare("UPDATE userlogin SET status = 'approved' WHERE id = ?");
            if ($updateStmt === false) {
                die("Error preparing the UPDATE query: " . $conn->error);
            }
            $updateStmt->bind_param("i", $userId);
            if (!$updateStmt->execute()) {
                die("Error executing the UPDATE query: " . $updateStmt->error);
            }

            sendNotificationEmail($user['name'], $user['email'], 'Approved');
        } else {
            $errorMessage = "User not found with the given ID.";
        }

        // Redirect to refresh the page and show the updated list of users
        header("Location: message.php");
        exit();
    }

    // Reject user
    if (isset($_GET['reject'])) {
        $userId = $_GET['reject'];

        // Prepare and execute the UPDATE statement
        $stmt = $conn->prepare("UPDATE userlogin SET status = 'rejected' WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing the UPDATE query: " . $conn->error);
        }
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            die("Error executing the UPDATE query: " . $stmt->error);
        }
        $deleteLastRecordStmt = $conn->prepare("DELETE FROM building WHERE id = (SELECT MAX(id) FROM building)");
    if ($deleteLastRecordStmt === false) {
        die("Error preparing the DELETE query: " . $conn->error);
    }
    if (!$deleteLastRecordStmt->execute()) {
        die("Error executing the DELETE query: " . $deleteLastRecordStmt->error);
    }
        // Fetch user email to send notification
        $userQuery = $conn->prepare("SELECT email, name FROM userlogin WHERE id = ?");
        if ($userQuery === false) {
            die("Error preparing the SELECT query for rejection: " . $conn->error);
        }
        $userQuery->bind_param("i", $userId);
        if (!$userQuery->execute()) {
            die("Error executing the SELECT query for rejection: " . $userQuery->error);
        }
        $user = $userQuery->get_result()->fetch_assoc();
        sendNotificationEmail($user['name'], $user['email'], 'Rejected');

        // Redirect to refresh the page and show the updated list of users
        header("Location: message.php");
        exit();
    }

    // Close the connection
    $conn->close();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Message Page</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            width: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            background: #6793AC;
            padding: 15px;
            color: white;
            border-radius: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .logout {
            padding: 10px 15px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .approve-button, .reject-button {
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
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
        .zoomable-image {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        </style>
    </head>
    <body>
        <div class="sidebar">
            <h2 style="font-size:20px;">📩Messages Dashboard</h2>
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
                <h1>📩Messages Dashboard</h1>
                <a href="loginpage.php"><button class="logout">Logout</button></a>
            </div>

            <?php if (isset($errorMessage)): ?>
                <div class="alert error"><?= $errorMessage ?></div>
            <?php endif; ?>

            <table class="table">
                <thead>
                    <tr>
                        <th>Flat</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Aadhaar No</th>
                        <th>Image</th>
                        <th>Owner/Rental</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingUsers as $user): ?>
                        <tr>
                            <td><?= $user['flat'] ?></td>
                            <td><?= $user['name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['acno'] ?></td>
                            <td>
                                <img src="<?= $user['image_path'] ?>" alt="User Image" width="100" class="zoomable-image">
                            </td>
                            <td><?= $user['who'] ?></td>
                            <td>
                                <a href="message.php?approve=<?= $user['id'] ?>" class="approve-button">Approve</a>
                                <a href="message.php?reject=<?= $user['id'] ?>" class="reject-button">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <script>
        // Function to hide alert message after 5 seconds
        window.onload = function() {
            const successMessage = document.querySelector('.alert.success');
            const errorMessage = document.querySelector('.alert.error');
            
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 5000);
            }

            if (errorMessage) {
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 5000);
            }
        };
        
        // jQuery function for image zoom on hover
        $(document).ready(function() {
            $('.zoomable-image').hover(
                function() {
                    $(this).css('transform', 'scale(1.5)');
                    $(this).css('z-index', '1000');
                    $(this).css('position', 'relative');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                    $(this).css('z-index', '0');
                }
            );
        });
        </script>
    </body>
    </html>