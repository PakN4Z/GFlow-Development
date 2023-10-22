<?php
include 'db_connection.php';

$id = $_POST['id'];

$sql = "DELETE FROM postprocessors WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Postprocessor deleted successfully!";
} else {
    echo "Error deleting postprocessor: " . $conn->error;
}

$conn->close();
?>
