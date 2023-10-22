<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GFlow</title>
    <link rel="stylesheet" href="style/gflow-style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
</head>
<body>
<div class="container">
    <div class="sidebar">
        <!-- Navigation Menu -->
				<ul class="navbar">
			<li>
				<a href="view_blanks.php" target="content-frame">Blanks</a>
				
			</li>
			<li>
				<span class="toggle-btn">+ Settings</span>
				<ul class="submenu">
					<li><a href="machines_view.php" target="content-frame">Machines</a></li>
					<li><a href="postprocessors_view.php" target="content-frame">Post processors</a></li>
					<li><a href="materials_view.php" target="content-frame">Materials</a></li>
				</ul>
			</li>
            <li>
                <span class="toggle-btn">+ Orders</span>
                <ul class="submenu">
                    <li><a href="#">New orders</a></li>
                    <li>
                        <span class="toggle-btn">+ Milling calculations</span>
                        <ul class="submenu">
                            <li><a href="#">Hyperdent calculations</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>
                <span class="toggle-btn">+ Milling machines</span>
                <ul class="submenu">
                    <li><a href="#">1st machine</a></li>
                    <li><a href="#">2nd machine</a></li>
                    <li><a href="#">3rd machine</a></li>
                </ul>
            </li>
            <li><a href="#">Finished</a></li>
            <li><a href="#">Sintering</a></li>
            <li><a href="#">Final check</a></li>
            <li><a href="#">Archive</a></li>
        </ul>
    </div>
    <div class="content">
        <iframe name="content-frame" src="view_blanks.php"></iframe>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="js/custom.js"></script>
<script>
    $(document).ready(function() {
    $(".toggle-btn").click(function() {
        // Toggle the submenu of the clicked button
        $(this).siblings(".submenu").slideToggle();

        
    });
});

</script>
</body>
</html>

