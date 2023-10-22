<?php
include 'db_connection.php';

// Get the data from the POST request
$id = $_POST['id'];
$type = $_POST['type'];
$description = $_POST['description'];
$ip = $_POST['ip'];
$ping = $ip; // PING is the same as IP

// Prepare the SQL query to update the postprocessor
$sql = "UPDATE postprocessors SET 
            type = '$type', 
            description = '$description', 
            ip = '$ip', 
            ping = '$ping' 
        WHERE id = $id";

// Execute the query and check the result
if ($conn->query($sql) === TRUE) {
    echo "Postprocessor updated successfully!";
} else {
    echo "Error updating postprocessor: " . $conn->error;
}

$conn->close();
?>
