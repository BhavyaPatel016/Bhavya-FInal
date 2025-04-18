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

// Initialize error flags
$errors = [];

// Initialize form input variables
$residency_name = $number_of_floors = $flats_per_floor = $flat_sequence = $residency_address = "";
$residency_exists = false;  // Flag to check if residency already exists

// Check if residency already exists in the database
$check_query = "SELECT * FROM residency LIMIT 1"; // Limit to 1 row for checking if any residency exists
$result = $conn->query($check_query);
if ($result->num_rows > 0) {
    $residency_exists = true;  // Residency already exists in the database
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$residency_exists) {
    // Collect the form data
    $residency_name = $_POST['residency-name'];
    $number_of_floors = $_POST['floors'];
    $flats_per_floor = $_POST['flats-per-floor'];
    $flat_sequence = $_POST['flat-sequence'];
    $residency_address = $_POST['residency-address'];

    // Validate residency name (only alphabetic characters and spaces)
    $namePattern = "/^[A-Za-z\s]+$/";
    if (!preg_match($namePattern, $residency_name)) {
        $errors['residency_name']  = "<br> Residency name must contain only alphabets and spaces, without numbers.";
    }

    // Validate number of floors (only numeric values)
    if (!is_numeric($number_of_floors) || $number_of_floors <= 0) {
        $errors['floors'] = "<br>Please enter a valid number for floors.";
    }

    // Validate flats per floor (only numeric values)
    if (!is_numeric($flats_per_floor) || $flats_per_floor <= 0) {
        $errors['flats_per_floor'] = "<br>Please enter a valid number for flats per floor.";
    }

    // If there are no errors and residency doesn't exist, proceed with database insertion
    if (empty($errors) && !$residency_exists) {
        // Prepare SQL query to insert data into the residency table
        $sql = "INSERT INTO residency (residency_name, number_of_floors, flats_per_floor, flat_sequence, residency_address) 
                VALUES ('$residency_name', '$number_of_floors', '$flats_per_floor', '$flat_sequence', '$residency_address')";

        // Execute the query to insert into residency table
        if ($conn->query($sql) === TRUE) {
            // Get the last inserted residency id
            $residency_id = $conn->insert_id;  // Using insert_id to get the ID of the inserted residency

            // Now, generate the flat sequence for the building_information table
            for ($floor = 1; $floor <= $number_of_floors; $floor++) {
                for ($flat = 1; $flat <= $flats_per_floor; $flat++) {
                    // Generate the flat number with correct formatting
                    $flat_number = $floor . str_pad($flat, 2, '0', STR_PAD_LEFT);

                    // Insert into building_information table, including the floor column
                    $sql_building = "INSERT INTO building_information (flat, residency_id, floor) 
                                     VALUES ('$flat_number', '$residency_id', '$floor')";

                    if (!$conn->query($sql_building)) {
                        echo "Error: " . $sql_building . "<br>" . $conn->error;
                    }
                }
            }

            // Redirect to the same page to reload
            header("Location: residency_details.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Existing styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 5px;
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

        form:hover {
            transform: translateY(-5px);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .input-container {
            width: 700px;
            padding: 30px;
            border: 0px solid #ddd;
            border-radius: 05px;
            margin-bottom: 50px;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1);
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

        .disabled-input {
            background-color: #f0f0f0;
            pointer-events: none;  /* Disable interaction */
            color: #999;
        }

        /* CSS for Next button */
        .next-button {
            position: fixed;
            bottom: 40px;
            right: 30px;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s ease;
        }

        .next-button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Dashboard</h2>
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
        <h1>Profile Page</h1>
        <a href="loginpage.php"><button class="logout">Logout</button></a>
    </div>

    <?php if ($residency_exists): ?>
        <p class="error" style="color:red;">Residency details have already been entered. You cannot add them again.</p>
    <?php else: ?>
        <form method="POST" action="adminpage.php">
            <div class="input-container">
                <div class="form-group">
                    <label for="residency-name">Residency Name</label>
                    <input type="text" id="residency-name" name="residency-name" value="<?php echo htmlspecialchars($residency_name ?? ''); ?>" required>
                    <?php if (isset($errors['residency_name'])): ?>
                        <span class="error"><?php echo $errors['residency_name']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="floors">Number of Floors</label>
                    <input type="text" id="floors" name="floors" value="<?php echo htmlspecialchars($number_of_floors ?? ''); ?>" required>
                    <?php if (isset($errors['floors'])): ?>
                        <span class="error"><?php echo $errors['floors']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="flats-per-floor">Number of Flats per Floor</label>
                    <input type="text" id="flats-per-floor" name="flats-per-floor" value="<?php echo htmlspecialchars($flats_per_floor ?? ''); ?>" required>
                    <?php if (isset($errors['flats_per_floor'])): ?>
                        <span class="error"><?php echo $errors['flats_per_floor']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="flat-sequence">Flat Sequence</label>
                    <input type="text" id="flat-sequence" name="flat-sequence" value="<?php echo htmlspecialchars($flat_sequence ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="residency-address">Residency Address</label>
                    <input type="text" id="residency-address" name="residency-address" value="<?php echo htmlspecialchars($residency_address ?? ''); ?>" required>
                </div>
                <button type="submit">Submit</button>
            </div>
        </form>
    <?php endif; ?>
    <a href="residency_details.php">
        <button class="next-button">Next</button>
    </a>
</div>

</body>
</html>
