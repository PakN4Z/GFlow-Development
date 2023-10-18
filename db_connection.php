<?php
$servername = "localhost";  // Your server name, usually "localhost" for local installations
$username = "root";         // Your database username, default for XAMPP is "root"
$password = "";             // Your database password, default for XAMPP is an empty string
$dbname = "gflow_db";       // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
