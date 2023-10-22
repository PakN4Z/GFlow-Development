<?php
include 'db_connection.php';

// Get the data from the POST request
$id = $_POST['id'];
$name = $_POST['name'];
$machine_type = $_POST['machine_type'];
$nc_extension = $_POST['nc_extension'];
$remote_ip = $_POST['remote_ip'];
$auto_sleep = $_POST['auto_sleep'];

// Prepare the SQL query to update the machine
$sql = "UPDATE machines SET 
            name = '$name', 
            machine_type = '$machine_type', 
            nc_extension = '$nc_extension', 
            remote_ip = '$remote_ip', 
            auto_sleep = '$auto_sleep' 
        WHERE id = $id";

// Execute the query and check the result
if ($conn->query($sql) === TRUE) {
    echo "Machine updated successfully!";
} else {
    echo "Error updating machine: " . $conn->error;
}

$conn->close();
?>
