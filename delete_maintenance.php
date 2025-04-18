<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "project1";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // SQL query to delete the record
    $sql = "DELETE FROM admin_maintenance WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Record deleted successfully!'); window.location.href='m_admin.php';</script>";
    } else {
        echo "<script>alert('Error deleting record!'); window.location.href='m_admin.php';</script>";
    }
}

mysqli_close($conn);
?>
