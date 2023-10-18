<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blank</title>
</head>
<body>
    <h2>Add New Blank</h2>
    <form action="save_blank.php" method="post">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" step="0.01" required><br><br>

        <label for="thickness">Thickness (mm):</label>
        <input type="number" id="thickness" name="thickness" step="0.01" required><br><br>

        <label for="diameter">Diameter (mm):</label>
        <input type="number" id="diameter" name="diameter" step="0.01" required><br><br>

        <label for="scaling_factor">Scaling Factor:</label>
        <input type="number" id="scaling_factor" name="scaling_factor" step="0.01" value="1" required><br><br>

        <label for="lot_number">LOT NUMBER:</label>
        <input type="text" id="lot_number" name="lot_number" required><br><br>

        <label for="material">Material:</label>
        <select id="material" name="material">
            <?php
            // Fetch materials from the database and populate the dropdown
            include 'db_connection.php'; // Include your database connection file
            $result = $conn->query("SELECT name FROM materials");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="comments">Comments:</label>
        <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br><br>

        <input type="submit" value="Add Blank">
    </form>
</body>
</html>
