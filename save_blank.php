<?php
// Database connection
include 'db_connection.php';

// Retrieve form data
$quantity = $_POST['quantity'];
$thickness = $_POST['thickness'];
$diameter = $_POST['diameter'];
$scaling_factor = $_POST['scaling_factor'];
$lot_number = $_POST['lot_number'];
$material = $_POST['material'];
$comments = $_POST['comments'];

$success = true; // Assume success initially

// Loop through the quantity and insert each blank into the database
for ($i = 0; $i < $quantity; $i++) {
    $sql = "INSERT INTO blanks (thickness, diameter, scaling_factor, lot_number, material, comments) 
            VALUES ('$thickness', '$diameter', '$scaling_factor', '$lot_number', '$material', '$comments')";

    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
        $success = false; // Set success to false if there's an error
        break;  // Exit the loop if there's an error
    }
}

if ($success) {
    echo "<script>document.getElementById('myModal').style.display = 'none';</script>";
    echo "<script>alert('Blank added successfully!');</script>";
    echo "<script>window.location.href = 'view_blanks.php';</script>"; // Redirect back to the view_blanks page
} else {
    echo "<script>alert('Error adding blank!');</script>";
}

$conn->close();
?>
