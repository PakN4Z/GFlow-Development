<?php
// Database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password
$dbname = "gflow_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$name = $_POST['name'];
$description = $_POST['description'];
$density = $_POST['density'];

// Insert data into database
$sql = "INSERT INTO materials (name, description, density) 
        VALUES ('$name', '$description', '$density')";

if ($conn->query($sql) === TRUE) {
    echo "New material added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


$conn->close();
?>
