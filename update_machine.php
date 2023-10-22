<?php
include 'db_connection.php';

$id = $_POST['id'];
$name = $_POST['name'];
$ncExtension = $_POST['ncExtension'];
$remoteIp = $_POST['remoteIp'];

$sql = "UPDATE machines SET name='$name', ncExtension='$ncExtension', remoteIp='$remoteIp' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Machine updated successfully!";
} else {
    echo "Error updating machine: " . $conn->error;
}

$conn->close();
?>
