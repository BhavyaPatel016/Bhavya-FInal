
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'PHPMailer/PHPMailer.php'; // Adjust the path if necessary
require 'PHPMailer/SMTP.php'; // Adjust the path if necessary
require 'PHPMailer/Exception.php'; // Adjust the path if necessary

$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "project1"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if (isset($_POST['sign-up'])) {
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $number = $_POST['number'];
    $acno   = $_POST['acno'];   
    $password = $_POST['setpassword'];
    $confirmPassword = $_POST['confirmpassword'];

    // Error handling for password mismatch
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!<br>";
        exit;
    }

    $imagePath = ''; // Initialize the image path
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION); // Get file extension
        $imageName = $acno . '.' . $imageExtension; // Rename image to Aadhaar number
        $imageTmp = $image['tmp_name'];
        $imagePath = 'uploads/' . $imageName; // Define the upload directory

        // Check if file size is larger than 2MB
        if ($image['size'] > 2000000) {
            echo "Error: The image size should not exceed 2MB.<br>";
            exit;
        }

        // Move the uploaded file to the server
        if (!move_uploaded_file($imageTmp, $imagePath)) {
            echo "Error uploading image!<br>";
            exit;
        }
    }

    // Hash the password before storing it
    $hashedPassword = ($password);  

    // 1. Fetch all building details from the building table (without filtering by building_id)
    $buildingQuery = "SELECT floor, flat, who, purchaseDate, rentalDate FROM building";
    
    $buildingStmt = $conn->prepare($buildingQuery);

    // Check if the prepare was successful
    if ($buildingStmt === false) {
        die('Error preparing SELECT query: ' . $conn->error);
    }

    $buildingStmt->execute();
    $buildingStmt->store_result();

    // Check if any data was returned
    if ($buildingStmt->num_rows > 0) {
        $buildingStmt->bind_result($floor, $flat, $who, $purchase_date, $rental_date);
        
        // Fetch and display building data for each row
        while ($buildingStmt->fetch()) {
            // You can store the last fetched building data for insertion
            // This will be used in the user registration query
        }
    } else {
        echo "No building data found.<br>";
        exit;
    }

    // Step 2: Insert user data along with building details into the `userlogin` table
    $insertUserQuery = "INSERT INTO userlogin (name, number, email, acno, image_path, password, floor, flat, who, purchaseDate, rentalDate) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($insertUserQuery)) {
        // Bind the parameters in the correct order (assuming using the last fetched building record for insertion)
        $stmt->bind_param("sssssssssss", $name, $number, $email, $acno, $imagePath, $hashedPassword, $floor, $flat, $who, $purchase_date, $rental_date);

        // Execute the insert query
        if ($stmt->execute()) {
            echo "User  registered successfully!<br>";
            // Send email notification RECIEVER
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through

                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'ressiment@gmail.com';                // SMTP username
                $mail->Password   = 'llyn fmwo nkzj kzpk'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          // Enable implicit TLS encryption
                $mail->Port       = 465;                                    // TCP port to connect to

                // Recipients
                $mail->setFrom($email,$name);
                $mail->addAddress("bhavyapatel1216@gmail.com", 'ADMIN'); // Add a recipient

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Notification About the user';
                $mail->Body    = "A new user has registered:<br>Name: $name<br>Email: $email<br>Phone Number: $number";

                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Redirect to homepage or any other page after successful registration
            header("Location: homepage.php");
            exit;
        } else {
            echo "Error executing user registration query: " . $stmt->error . "<br>";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error preparing the user registration query
        echo "Error preparing user registration statement: " . $conn->error . "<br>";
    }

    // Close the building statement
    $buildingStmt->close();
}
// Close the database connection
$conn->close();
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Register Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome for Camera Icon -->
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
            padding-left: 150px;
            color: #333;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 55px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
            margin-left: 50px;
        }
        label {
            font-size: 15px;
            font-weight: bold;
        }
        h1 {
            margin-bottom: 20px;
            color: green;
            text-align: center;
        }
        .input-wrapper {
            position: relative;
            margin-bottom: 15px;
        }
        input {
            width: calc(100% - 40px);
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #0099CC;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
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
            background-color: #45a049;
            transform: scale(1.05);
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .back-button {
            background-color: green;
            flex: 1;
            margin-left: 10px;
        }
        .back-button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 12px ;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        .fas.fa-eye, .fas.fa-eye-slash {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #555;
        }

        .fas.fa-eye-slash {
            color: #0099cc; /* Change color when password is visible */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <!-- Name -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter Name" oninput="validateName()" required>
            <div id="name-error" class="error"></div>

            <!-- Phone Number -->
            <label for="number">Phone Number:</label>
            <input type="text" id="number" name="number" maxlength="10" placeholder="Enter Phone Number" oninput="validatePhoneNumber()" required>
            <div id="phone-error" class="error"></div>

            <!-- Email -->
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter Email" oninput="validateEmail()" required>
            <div id="email-error" class="error"></div>

            <!-- Aadhaar Number -->
            <label for="acno">Aadhaar Number:</label>
            <input type="text" id="acno" name="acno" placeholder="Enter Aadhaar Number" oninput="formatAadhaarNumber(this)" required>
            <div id="aadhaar-error" class="error"></div>

            <!-- Aadhaar Image -->
            <label for="image">Aadhaar Image:</label>
            <input type="file" id="image" name="image" placeholder="Upload Aadhaar Image" accept="image/*">

            <!-- Set Password --><label for="setpassword">Set Password:</label>
            <div class="input-wrapper">
            <input type="password" id="setpassword" name="setpassword" placeholder="Enter Password" required>
            <i class="fas fa-eye" id="toggle-password"></i>
            </div>
            <div id="password-error" class="error"></div>

            <!-- Confirm Password -->
            <label for="confirmpassword">Confirm Password:</label>
            <div class="input-wrapper">
            <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" required>
            <i class="fas fa-eye" id="toggle-confirm-password"></i> 
            </div>
            <div id="confirm-password-error" class="error"></div>


            <!-- Submit Button -->
            <div class="button-group">
                <button type="submit" name="sign-up">Sign Up</button>
                <a href="registerpage.php"><button type="button" class="back-button">Back</button></a>
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
        let isValid = true; // Flag to track the validity of the form

        // Validate name
        validateName();
        if (document.getElementById("name-error").textContent !== "") {
            isValid = false;
        }

        // Validate phone number
        validatePhoneNumber();
        if (document.getElementById("phone-error").textContent !== "") {
            isValid = false;
        }

        // Validate email
        validateEmail();
        if (document.getElementById("email-error").textContent !== "") {
            isValid = false;
        }

        // Validate Aadhaar number
        const aadhaarInput = document.getElementById("acno");
        if (aadhaarInput.value.replace(/[^0-9]/g, '').length !== 12) {
            document.getElementById("aadhaar-error").textContent = "Aadhaar number must be exactly 12 digits!";
            isValid = false;
        } else {
            document.getElementById("aadhaar-error").textContent = "";
        }

        // Validate passwords
        const password = document.getElementById("setpassword").value;
        const confirmPassword = document.getElementById("confirmpassword").value;
        if (password !== confirmPassword) {
            document.getElementById("confirm-password-error").textContent = "Passwords do not match!";
            isValid = false;
        } else if (password.length < 6) {
            document.getElementById("password-error").textContent = "Password must be at least 6 characters long!";
            isValid = false;
        } else {
            document.getElementById("password-error").textContent = "";
            document.getElementById("confirm-password-error").textContent = "";
        }

        return isValid; // Prevent form submission if any input is invalid
    }
    // Name validation: Only allow alphabets and spaces
    function validateName() {
        const name = document.getElementById("name").value;
        const regex = /^[A-Za-z\s]+$/; // Only allow alphabets and spaces
        const errorMessage = document.getElementById("name-error");

        // Ensure the name has exactly 3 words and contains only alphabets and spaces
        const words = name.trim().split(/\s+/);
        if (words.length !== 3 || !regex.test(name)) {
            document.getElementById("name").value = name.replace(/[^A-Za-z\s]/g, ''); // Remove invalid characters
            errorMessage.textContent = "Name must consist of exactly 3 words (alphabets only).";
        } else {
            errorMessage.textContent = ""; // Clear any previous error message
        }
    }

    // Phone number validation: Must start with 6, 7, 8, or 9 and be exactly 10 digits
    function validatePhoneNumber() {
        const phoneNumberInput = document.getElementById("number");
        const phoneNumber = phoneNumberInput.value;
        const regex = /^[6-9][0-9]{9}$/; // Ensure starts with 6, 7, 8, or 9 and is exactly 10 digits
        const errorMessage = document.getElementById("phone-error");

        // Remove non-numeric characters
        phoneNumberInput.value = phoneNumber.replace(/[^0-9]/g, ''); 

        // Check if the phone number matches the regex for a valid phone number
        if (!regex.test(phoneNumberInput.value)) {
            errorMessage.textContent = "Phone number must start with 6, 7, 8, or 9 and be exactly 10 digits!";
        } else {
            errorMessage.textContent = ""; // Clear any previous error message
        }
    }

    // Aadhaar number validation: Limit to 12 digits and space after every 4 digits
    function formatAadhaarNumber(input) {
        let value = input.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        if (value.length > 12) {
            value = value.substring(0, 12); // Limit to 12 digits
        }
        value = value.replace(/(\d{4})(\d{4})(\d{4})/, '$1 $2 $3'); // Add space after every 4 digits
        input.value = value; // Update the input value with the formatted Aadhaar number

        // Validate the length of the Aadhaar number
        const errorMessage = document.getElementById("aadhaar-error");
        if (value.replace(/[^0-9]/g, '').length !== 12) {
            errorMessage.textContent = "Aadhaar number must be exactly 12 digits!";
        } else {
            errorMessage.textContent = ""; // Clear any previous error message
        }
    }

    // Email validation: Only allow valid characters in email
    function validateEmail() {
        const emailInput = document.getElementById("email");
        const email = emailInput.value;
        const regex = /^[a-zA-Z0-9._@]+$/;
        const errorMessage = document.getElementById("email-error");

        // Check if email contains invalid characters
        if (!regex.test(email)) {
            emailInput.value = email.replace(/[^a-zA-Z0-9._@]/g, '');
            errorMessage.textContent = "Email contains invalid characters!";
        } else if (!email.includes("@") || !email.includes(".")) {
            errorMessage.textContent = "Enter a valid email address!";
        } else {
            errorMessage.textContent = ""; // Clear any previous error message
        }
    }
    document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('setpassword');
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.classList.toggle('fa-eye-slash');
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            const confirmPasswordInput = document.getElementById('confirmpassword');
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>