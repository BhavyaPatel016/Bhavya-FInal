<?php
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

// Fetch floor data for dropdown
$query = "SELECT floor, flat FROM building_information ORDER BY floor, flat";
$result = $conn->query($query);

// Prepare a structure to group flats by floor
$floorData = [];
while ($row = $result->fetch_assoc()) {
    $floorData[$row['floor']][] = $row['flat'];
}

// Fetch rented flats
$rentedFlatsQuery = "SELECT floor, flat FROM building WHERE who = 'Rental'";
$rentedFlatsResult = $conn->query($rentedFlatsQuery);
$rentedFlats = [];
while ($row = $rentedFlatsResult->fetch_assoc()) {
    $rentedFlats[$row['floor']][] = $row['flat'];
}

// Fetch owned flats
$ownedFlatsQuery = "SELECT floor, flat FROM building WHERE who = 'Owner'";
$ownedFlatsResult = $conn->query($ownedFlatsQuery);
$ownedFlts = [];
while ($row = $ownedFlatsResult->fetch_assoc()) {
    $ownedFlts[$row['floor']][] = $row['flat'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $floor = isset($_POST['floor']) ? $_POST['floor'] : '';
    $flat = isset($_POST['flat']) ? $_POST['flat'] : '';
    $who = isset($_POST['who']) ? $_POST['who'] : '';
    $purchaseDate = isset($_POST['purdate']) ? $_POST['purdate'] : ''; // Capture purchase date
    $rentalDate = isset($_POST['rentdate']) ? $_POST['rentdate'] : null; // Capture rental date

    // Insert building data into the 'building' table
    $insertBuildingQuery = "INSERT INTO building (floor, flat, who, purchaseDate, rentalDate) 
                            VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($insertBuildingQuery)) {
        // Bind parameters to the query
        $stmt->bind_param("sssss", $floor, $flat, $who, $purchaseDate, $rentalDate);

        if ($stmt->execute()) {
            echo "Building data inserted successfully!<br>";
            // Redirect to homepage or any other page after successful insertion
            header("Location: u_registerpage.php");
            exit;
        } else {
            echo "Error executing building query: " . $stmt->error . "<br>";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Error preparing the statement
        echo "Error preparing building query: " . $conn->error . "<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
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
            border-radius: 30px;
            padding: 55px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
        }
        h1 {
            margin-bottom: 20px;
            color: green;
            text-align: center;
        }
        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        select, button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .date {
            width: 90%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        select:focus, button:focus {
            outline: none;
        }
        select {
            transition: border-color 0.3s;
        }
        select:focus {
            border-color: #0099CC;
        }
        button {
            width: 200px;
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
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        .back-button {
            background-color: green; 
            width: 90px;
        }
        .back-button:hover {
            background-color: #45a049;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="registerpage.php" method="POST" onsubmit="return handleSubmit(event)">
        <h1>REGISTER</h1>
        
        <label for="who">Who are you?</label><br>
        <input type="radio" id="owner" name="who" value="Owner" onchange="toggleDateFields()" required>
        <label for="owner" style="display: inline-block; margin-right: 20px;">Owner</label>
        <input type="radio" id="rental" name="who" value="Rental" onchange="toggleDateFields()" required>
        <label for="rental" style="display: inline-block;">Rental</label><br><br>

        <!-- Floor Input (Group Flats by Floor) -->
        <label for="floor">Select Floor:</label>
        <select name="floor" id="floor" onchange="updateFlats()">
            <option value="0">Select a Floor</option>
            <?php foreach ($floorData as $floor => $flats): ?>
                <option value="<?php echo $floor; ?>"><?php echo "Floor " . $floor; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Flat Input (Will be dynamically populated based on Floor selection) -->
        <label for="flat">Select Flat:</label>
        <select name="flat" id="flat">
            <option value="0">Select a Flat</option>
        </select>

        <!-- Purchase Date Section (Visible when Owner is selected) -->
        <div id="purchaseDateSection" class="hidden">
            <label for="purdate">Purchase Date:</label>
            <input type="date" name="purdate" id="purdate" min="2022-01-01" class="date" disabled>
        </div>

        <!-- Rental Date Section (Visible when Rental is selected) -->
        <div id="rentalDateSection" class="hidden">
            <label for="rentdate">Rental Date:</label>
            <input type="date" name="rentdate" id="rentdate" min="2022-01-01" class="date" disabled>
        </div>

        <!-- Submit Button -->
        <div class="button-container">
            <button type="submit">Next</button>
            <a href="loginpage.php"><button type="button" class="back-button">Back</button></a>
        </div>
    </form>
</div>

<script>
    // Function to toggle the visibility of the date fields based on selection
    function toggleDateFields() {
        const ownerRadio = document.getElementById('owner');
        const rentalRadio = document.getElementById('rental');
        const purchaseDateSection = document.getElementById('purchaseDateSection');
        const rentalDateSection = document.getElementById('rentalDateSection');
        const purdate = document.getElementById('purdate');
        const rentdate = document.getElementById('rentdate');
        const floorSelect = document.getElementById('floor');
        const flatSelect = document.getElementById('flat');

        // If "Owner" is selected
        if (ownerRadio.checked) {
            purchaseDateSection.classList.remove('hidden');
            purdate.disabled = false;
            rentalDateSection.classList.add('hidden');
            rentdate.disabled = true;

            // Clear the floor and flat selection when switching to "Owner" mode
            floorSelect.value = '0';
            flatSelect.innerHTML = '<option value="0">Select The Flat</option>';
        } 
        // If "Rental" is selected
        else if (rentalRadio.checked) {
            rentalDateSection.classList.remove('hidden');
            rentdate.disabled = false;
            purchaseDateSection.classList.add('hidden');
            purdate.disabled = true;

            // Clear the floor and flat selection when switching to "Rental" mode
            floorSelect.value = '0';
            flatSelect.innerHTML = '<option value="0">Select The Flat</option>';
        } else {
            purchaseDateSection.classList.add('hidden');
            rentalDateSection.classList.add('hidden');
            purdate.disabled = true;
            rentdate.disabled = true;
        }
    }

    // Function to update Flats based on the selected floor and rental/owner status
    function updateFlats() {
        var floorSelect = document.getElementById('floor');
        var flatSelect = document.getElementById('flat');
        var floor = floorSelect.value;
        var who = document.querySelector('input[name="who"]:checked').value;

        // Clear any previous options
        flatSelect.innerHTML = '<option value="0">Select The Flat</option>';

        // If a floor is selected, populate the flats
        if (floor !== '0') {
            <?php foreach ($floorData as $floor => $flats): ?>
                if (floor === "<?php echo $floor; ?>") {
                    <?php foreach ($flats as $flat): ?>
                        // If it's rental, show only flats that are owned but not rented
                        if (who === "Rental" && <?php echo json_encode(in_array($flat, $ownedFlts[$floor] ?? [])); ?> && !<?php echo json_encode(in_array($flat, $rentedFlats[$floor] ?? [])); ?>) {
                            flatSelect.innerHTML += '<option value="<?php echo $flat; ?>"><?php echo $flat; ?></option>';
                        } 
                        // For owner, show all flats excluding rented ones and owned ones
                        else if (who === "Owner" && !<?php echo json_encode(in_array($flat, $rentedFlats[$floor] ?? [])); ?> && !<?php echo json_encode(in_array($flat, $ownedFlts[$floor] ?? [])); ?>) {
                            flatSelect.innerHTML += '<option value="<?php echo $flat; ?>"><?php echo $flat; ?></option>';
                        }
                    <?php endforeach; ?>
                }
            <?php endforeach; ?>
        }
    }

    // Set max date for purchase and rental dates as today
    window.onload = function() {
        const today = new Date().toISOString().split("T")[0];
        document.getElementById('purdate').max = today;
        document.getElementById('rentdate').max = today;
    }
</script>
</body>
</html>
