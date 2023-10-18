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

// Loop through the quantity and insert each blank into the database
for ($i = 0; $i < $quantity; $i++) {
    $sql = "INSERT INTO blanks (thickness, diameter, scaling_factor, lot_number, material, comments) 
            VALUES ('$thickness', '$diameter', '$scaling_factor', '$lot_number', '$material', '$comments')";

    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
        exit;  // Exit the loop and script if there's an error
    }
}

echo $quantity . " blanks added successfully!";

$conn->close();
?>
