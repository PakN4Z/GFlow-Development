<?php
include 'db_connection.php';

$name = $_POST['name'];
$machineType = $_POST['machineType']; // This variable name is fine for PHP, but we'll change it in the SQL query
$ncExtension = $_POST['ncExtension'];
$remoteIp = $_POST['remoteIp'];
$autoSleep = $_POST['autoSleep'];

// Adjust the column names in the SQL query to match your database
$sql = "INSERT INTO machines (name, machine_type, nc_extension, remote_ip, auto_sleep) VALUES ('$name', '$machineType', '$ncExtension', '$remoteIp', '$autoSleep')";

if ($conn->query($sql) === TRUE) {
    echo "New machine added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
