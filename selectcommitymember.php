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

// SQL query to fetch all users from userlogin table who are approved by an admin
$sql = "SELECT * FROM userlogin1 WHERE who='owner'";  // Only fetch users with 'owner' role
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error executing query: " . $conn->error);  // Show the error message if query failed
}

$pendingUsers = [];
if ($result->num_rows > 0) {
    // Fetch all pending users
    while ($row = $result->fetch_assoc()) {
        $pendingUsers[] = $row;
    }
}

// Handle the approval logic if approve is clicked
if (isset($_GET['approve'])) {
    $userId = $_GET['approve'];

    // Fetch user details from userlogin table
    $userQuery = "SELECT * FROM userlogin WHERE id = ?";    
    $stmt = $conn->prepare($userQuery);

    // Check if prepare was successful
    if ($stmt === false) {
        die("Error preparing the SQL statement: " . $conn->error);  // Display specific error message
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();

        // Insert all user data into commity table (approved users)
        $insertQuery = "INSERT INTO commity (id, flat, name, email, number) 
                        VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);

        if ($insertStmt === false) {
            die("Error preparing the insert statement: " . $conn->error);
        }

        $insertStmt->bind_param("issss", $user['id'], $user['flat'], $user['name'], $user['email'], $user['number']);

        if ($insertStmt->execute()) {
            // After approval, update the user status to 'approved'
            $updateQuery = "UPDATE userlogin SET status = 'approved' WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $userId);
            $updateStmt->execute();
            $updateStmt->close();

            // Remove the user from the pendingUsers array (only for display)
            $pendingUsers = array_filter($pendingUsers, function($user) use ($userId) {
                return $user['id'] != $userId;
            });
            $pendingUsers = array_values($pendingUsers);  // Re-index the array

            $successMessage = "User approved successfully";
        } else {
            $errorMessage = "Failed to approve the user.";
        }

        $insertStmt->close();
    } else {
        $errorMessage = "User not found with the given ID.";
    }

    $stmt->close();
}

// Handle the rejection logic if reject is clicked
if (isset($_GET['reject'])) {
    $userId = $_GET['reject'];

    // Update the user's status to 'rejected' in the userlogin table
    $updateQuery = "UPDATE userlogin SET status = 'rejected' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    
    // Check if the query was prepared successfully
    if ($stmt === false) {
        die("Error preparing the SQL statement: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        // Remove the rejected user from the pendingUsers array (only for display)
        $pendingUsers = array_filter($pendingUsers, function($user) use ($userId) {
            return $user['id'] != $userId;
        });
        $pendingUsers = array_values($pendingUsers);  // Re-index the array

        $errorMessage = "User rejected.";
    } else {
        $errorMessage = "Failed to reject the user.";
    }

    $stmt->close();
}

// Handle the selection of committee members when the next button is clicked
if (isset($_POST['submit'])) {
    if (isset($_POST['commity']) && count($_POST['commity']) >= 3) { // Ensure at least 3 members are selected
        foreach ($_POST['commity'] as $selectedUser) { // Loop over selected users' IDs
            // Fetch user details from userlogin table using $selectedUser (which is the ID)
            $userQuery = "SELECT * FROM userlogin WHERE id = ?";
            $stmt = $conn->prepare($userQuery);
            $stmt->bind_param("i", $selectedUser); // Use the selected user ID
            $stmt->execute();
            $userResult = $stmt->get_result();

            if ($userResult->num_rows > 0) {
                $user = $userResult->fetch_assoc();

                // Insert selected user data into commity table
                $insertQuery = "INSERT INTO commity (flat, name, email, number) VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("isss", $user['flat'], $user['name'], $user['email'], $user['number']);
                $insertStmt->execute();
                $insertStmt->close();
            }
        }
        // Redirect to the next page
        header('Location: createcommity-president.php');
        exit();  // Ensure that script execution stops after the redirect
    } else {
        $errorMessage = "You must select at least 3 members.";
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Committee Page</title>
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
        transition: background-color 0.3s, transform 0.3s ease; /* Added transition for background and transform */
    }
    .sidebar a:hover {
        background: #5a7a87; /* Change background on hover */
        transform: scale(1.05); /* Slight scale up effect on hover */
    }
    .sidebar a {
        position: relative; /* Make it easier to apply animation */
    }
    .sidebar a::before {
        content: ''; /* Empty content for the pseudo-element */
        position: absolute;
        width: 100%;
        height: 2px; /* Thin line */
        background-color: #ffffff;
        bottom: 0; /* Position the line at the bottom */
        left: 0;
        transform: scaleX(0); /* Initially, the line is not visible */
        transform-origin: bottom right;
        transition: transform 0.3s ease; /* Transition for the line */
    }
    .sidebar a:hover::before {
        transform: scaleX(1); /* On hover, the line becomes visible */
        transform-origin: bottom left;
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
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 5px;
        color: #fff;
        display: none;
    }
    .alert.success {
        background-color: #4CAF50;
        border-color: #4CAF50;
    }
    .alert.error {
        background-color: #f44336;
        border-color: #f44336;
    }
        .button-container {
            text-align: left;
            margin: 20px;
        }
        button {
        width: 100px;
        padding: 12px;
        margin-top: 5px;
        border: none;
        border-radius: 30px;
        background-color: darkgreen;
        color: White;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
        font-size: 15px;
        margin-left:10px;
    }
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <div class="sidebar">
    <h2 style="font-size:20px;">👥 Committee Dashboard</h2>
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
            <h1>Select Committee Members</h1>
            <a href="loginpage.php"><button class="logout">Logout</button></a>
        </div>

        <h3></h3>
        
        <?php if (isset($errorMessage)) echo "<p style='color:red;'>$errorMessage</p>"; ?>
        <?php if (isset($successMessage)) echo "<p style='color:green;'>$successMessage</p>"; ?>

        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Flat</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingUsers as $user): ?>
                        <tr>                          
                            <td><?php echo $user['flat']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['number']; ?></td>
                            <td><input type="checkbox" name="commity[]" value="<?php echo $user['id']; ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="button-container">
                <button type="submit" name="submit" id="nextButton" disabled>Next</button>
            </div>
        </form>
    </div>

    <script>
        // Function to validate checkbox selection
        function validateCheckboxSelection() {
            const checkboxes = document.querySelectorAll('input[name="commity[]"]');
            let selectedCount = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedCount++;
                }
            });
            const nextButton = document.querySelector('button[type="submit"]');
            if (selectedCount >= 3) {
                nextButton.disabled = false;
                nextButton.style.opacity = 1;
                nextButton.style.cursor = 'pointer';
            } else {
                nextButton.disabled = true;
                nextButton.style.opacity = 0.5;
                nextButton.style.cursor = 'not-allowed';
            }
        }

        // Add event listener to checkboxes to validate selection on change
        document.querySelectorAll('input[name="commity[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', validateCheckboxSelection);
        });

        // Initialize the validation on page load
        validateCheckboxSelection();
    </script>

</body>
</html>
