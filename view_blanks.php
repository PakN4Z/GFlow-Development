<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Edit Blanks</title>
    <link rel="stylesheet" href="style/gflow-style.css"> <!-- Link to your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>All Blanks</h2>
	<div class="table-responsive">
    <table class="view-blanks-table" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thickness</th>
                <th>Diameter</th>
                <th>Scaling Factor</th>
                <th>LOT Number</th>
                <th>Material</th>
                <th>Location</th>
                <th>Comments</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'db_connection.php';
            $result = $conn->query("SELECT * FROM blanks");
            while ($row = $result->fetch_assoc()) {
                echo "<tr data-id='" . $row['id'] . "'>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td class='editable' data-column='thickness'>" . $row['thickness'] . "</td>";
                echo "<td class='editable' data-column='diameter'>" . $row['diameter'] . "</td>";
                echo "<td class='editable' data-column='scaling_factor'>" . $row['scaling_factor'] . "</td>";
                echo "<td class='editable' data-column='lot_number'>" . $row['lot_number'] . "</td>";
                echo "<td class='editable-dropdown' data-column='material' data-value='" . $row['material'] . "'>" . $row['material'] . "</td>";
                echo "<td class='editable-dropdown' data-column='location' data-value='" . $row['location'] . "'>" . $row['location'] . "</td>";
                echo "<td class='editable' data-column='comments'>" . $row['comments'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
	</div>

    <script>
        $(document).ready(function() {
            // Enable inline editing
            $(".editable").click(function() {
                var originalContent = $(this).text();
                $(this).html("<input type='text' value='" + originalContent + "' />");
                $(this).children().first().focus();

                $(this).children().first().blur(function() {
                    var newContent = $(this).val();
                    var column = $(this).parent().data("column");
                    var id = $(this).closest("tr").data("id");
                    $(this).parent().text(newContent);

                    // Save changes using AJAX
                    $.post("update_blank.php", { id: id, column: column, value: newContent }, function(data) {
                        if (data !== "success") {
                            alert("Error updating record!");
                        }
                    });
                });
            });

            // Enable dropdown editing for Material and Location columns
            $(".editable-dropdown").click(function() {
                var column = $(this).data("column");
                var currentValue = $(this).data("value");
                if (column === "material") {
                    // Fetch materials from the database and populate the dropdown
                    var dropdown = "<select>";
                    <?php
                    $materials = $conn->query("SELECT name FROM materials");
                    while ($material = $materials->fetch_assoc()) {
                        echo "dropdown += '<option value=\"" . $material['name'] . "\">" . $material['name'] . "</option>';";
                    }
                    ?>
                    dropdown += "</select>";
                } else if (column === "location") {
                    var dropdown = "<select>";
                    dropdown += "<option value='STOCK'>STOCK</option>";
                    dropdown += "<option value='MACHINE'>MACHINE</option>";
                    dropdown += "<option value='ARCHIVE'>ARCHIVE</option>";
                    dropdown += "</select>";
                }
                $(this).html(dropdown);
                $(this).find("select").val(currentValue).focus();

                // Save changes when dropdown value changes
                $(this).find("select").change(function() {
                    var newValue = $(this).val();
                    var column = $(this).parent().data("column");
                    var id = $(this).closest("tr").data("id");
                    $(this).parent().data("value", newValue).text(newValue);

                    // Save changes using AJAX
                    $.post("update_blank.php", { id: id, column: column, value: newValue }, function(data) {
                        if (data !== "success") {
                            alert("Error updating record!");
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
