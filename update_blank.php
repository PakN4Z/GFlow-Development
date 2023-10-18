<?php
include 'db_connection.php';

$id = $_POST['id'];
$column = $_POST['column'];
$value = $_POST['value'];

$sql = "UPDATE blanks SET $column = '$value' WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
