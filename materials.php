<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Material</title>
</head>
<body>
    <h2>Add New Material</h2>
    <form action="save_material.php" method="post">
        <label for="name">Material Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>

        <label for="density">Density:</label>
        <input type="number" id="density" name="density" step="0.01" required><br><br>

        <input type="submit" value="Add Material">
    </form>
</body>
</html>
