<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php';

$id = $_POST['id'];
$thickness = $_POST['thickness'];
$diameter = $_POST['diameter'];
$scaling_factor = $_POST['scaling_factor'];
$lot_number = $_POST['lot_number'];
$location = $_POST['location'];
$comments = $_POST['comments'];
$material = $_POST['material'];  // Get the material from POST data

$query = "UPDATE blanks SET thickness=?, diameter=?, scaling_factor=?, lot_number=?, location=?, comments=?, material=? WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("dddsdsss", $thickness, $diameter, $scaling_factor, $lot_number, $location, $comments, $material, $id);
$result = $stmt->execute();

if ($result) {
    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
