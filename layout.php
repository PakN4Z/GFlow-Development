<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GFlow</title>
    <link rel="stylesheet" href="style/gflow-style.css"> <!-- Link to your main CSS file -->

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <!-- Navigation Menu -->
        <ul>
            <li>
                <a href="view_blanks.php" target="content-frame">Blanks</a>
                <!-- Subdirectories for Blanks -->
                <ul>
                    <li><a href="stock.php" target="content-frame">STOCK</a></li>
                    <li><a href="machine.php" target="content-frame">MACHINE</a></li>
                    <li><a href="archive.php" target="content-frame">ARCHIVE</a></li>
                </ul>
            </li>
            <!-- Add more links as needed -->
        </ul>
    </div>
    <div class="content">
        <!-- Content Frame -->
        <iframe name="content-frame" src="view_blanks.php"></iframe>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<!-- Custom JS -->
<script src="js/custom.js"></script>

</body>
</html>
