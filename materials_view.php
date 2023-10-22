<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Edit Materials</title>
    <link rel="stylesheet" href="style/gflow-style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
</head>
<body>
    <h2>All Materials</h2>
    <button id="addMaterialBtn">Add Material</button>
    
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add new Material</h3>
            <form action="save_material.php" method="post">
                <label for="name">Material Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>

                <label for="density">Density:</label>
                <input type="number" id="density" name="density" step="0.01" required><br><br>

                <input type="submit" value="Add Material">
            </form>
        </div>
    </div>

    <!-- Table to display materials (similar to blanks table) -->
    <div class="table-responsive">
        <table class="view-materials-table" border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Material Name</th>
                    <th>Description</th>
                    <th>Density</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_connection.php';
                $result = $conn->query("SELECT * FROM materials");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . $row['id'] . "'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td class='editable' data-column='name'>" . $row['name'] . "</td>";
                    echo "<td class='editable' data-column='description'>" . $row['description'] . "</td>";
                    echo "<td class='editable' data-column='density'>" . $row['density'] . "</td>";
                    echo "<td><button class='edit-row-btn'>Edit</button> <button class='delete-row-btn'>X</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript for modal and table editing (similar to blanks_view.php) -->
    <script>
	function enableEditing(row) {
    row.addClass('editing-row');
    
    row.find('.editable').each(function() {
        var cell = $(this);
        var originalContent = cell.text();
        cell.html("<input type='text' value='" + originalContent + "' />");
    });
}

function disableEditing(row) {
    row.removeClass('editing-row');
    
    var data = {
        id: row.data("id")
    };
    
    row.find('.editable').each(function() {
        var cell = $(this);
        var inputValue = cell.find('input').val();
        var column = cell.data("column");
        data[column] = inputValue;
        cell.text(inputValue);
    });

    $.post("update_material.php", data, function(response) {
        if (response !== "success") {
            alert("Error updating record!");
        }
    });
}

        // ... (similar to the JavaScript in blanks_view.php)
		$(document).ready(function() {
    $(".edit-row-btn").click(function() {
        var row = $(this).closest('tr');
        var btn = $(this);

        if (btn.text() === 'Edit') {
            enableEditing(row);
            btn.text('Done');
        } else {
            disableEditing(row);
            btn.text('Edit');
        }
    });

    $(".delete-row-btn").click(function() {
        var row = $(this).closest('tr');
        var id = row.data("id");
        var confirmation = confirm("Delete forever?");

        if (confirmation) {
            $.post("delete_material.php", { id: id }, function(data) {
                if (data === "success") {
                    row.remove();
                } else {
                    alert("Error deleting record!");
                }
            });
        }
    });
});

var modal = document.getElementById("myModal");
var btn = document.getElementById("addMaterialBtn");
var span = document.getElementsByClassName("close")[0];

btn.onclick = function() {
    modal.style.display = "block";
}

span.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

    </script>

</body>
</html>
