<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "project1";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer to prevent SQL injection

    // SQL query to delete the record
    $sql = "DELETE FROM onepayment WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Record deleted successfully');
                window.location.href = 'm_admin.php'; // Redirect back to maintenance page
              </script>";
    } else {
        echo "<script>
                alert('Error deleting record: " . mysqli_error($conn) . "');
                window.location.href = 'm_admin.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid request');
            window.location.href = 'm_admin.php';
          </script>";
}

// Close connection
mysqli_close($conn);
?>
