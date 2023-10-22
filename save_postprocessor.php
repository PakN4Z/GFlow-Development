<?php
include 'db_connection.php';

$type = $_POST['type'];
$description = $_POST['description'];
$ip = $_POST['ip'];
$ping = $ip; // PING is the same as IP

$sql = "INSERT INTO postprocessors (type, description, ip, ping) VALUES ('$type', '$description', '$ip', '$ping')";

if ($conn->query($sql) === TRUE) {
    echo "New postprocessor added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
