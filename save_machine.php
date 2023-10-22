<?php
include 'db_connection.php';

$name = $_POST['name'];
$machineType = $_POST['machineType'];
$ncExtension = $_POST['ncExtension'];
$remoteIp = $_POST['remoteIp'];
$autoSleep = $_POST['autoSleep'];

$sql = "INSERT INTO machines (name, machineType, ncExtension, remoteIp, autoSleep) VALUES ('$name', '$machineType', '$ncExtension', '$remoteIp', '$autoSleep')";

if ($conn->query($sql) === TRUE) {
    echo "New machine added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
