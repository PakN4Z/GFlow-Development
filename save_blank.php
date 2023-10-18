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
$quantity = $_POST['quantity'];
$thickness = $_POST['thickness'];
$diameter = $_POST['diameter'];
$scaling_factor = $_POST['scaling_factor'];
$lot_number = $_POST['lot_number'];
$material = $_POST['material'];
$comments = $_POST['comments'];

// Insert data into database
$sql = "INSERT INTO blanks (quantity, thickness, diameter, scaling_factor, lot_number, material, comments) 
        VALUES ('$quantity', '$thickness', '$diameter', '$scaling_factor', '$lot_number', '$material', '$comments')";

if ($conn->query($sql) === TRUE) {
    echo "New blank added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
