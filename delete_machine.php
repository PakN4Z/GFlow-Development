<?php
include 'db_connection.php';

$id = $_POST['id'];

$sql = "DELETE FROM machines WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Machine deleted successfully!";
} else {
    echo "Error deleting machine: " . $conn->error;
}

$conn->close();
?>
