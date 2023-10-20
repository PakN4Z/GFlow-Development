<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GFlow</title>
    <link rel="stylesheet" href="style/gflow-style.css"> <!-- Link to your main CSS file -->
</head>
<body>
<div class="container">
    <div class="sidebar">
        <!-- Navigation Menu -->
        <ul>
            <li><a href="add_blank.php" target="content-frame">Add Blank</a></li>
            <li><a href="view_blanks.php" target="content-frame">View Blanks</a></li>
            <!-- Add more links as needed -->
        </ul>
    </div>
    <div class="content">
        <!-- Content Frame -->
        <iframe name="content-frame" src="add_blank.php"></iframe>
    </div>
</div>

<!-- Include jQuery and custom JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/custom.js"></script> <!-- Replace with the path to your JS file -->

</body>
</html>
