<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Edit Blanks</title>
    <link rel="stylesheet" href="style/gflow-style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
</head>
<body>
    <h2>All Blanks</h2>
    <button id="addBlankBtn">Add Blank</button>
    
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
			<h3>Add new Blank</h3> <!-- Add this line -->
			
			<form action="save_blank.php" method="post" class="blank-form">
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
            $result = $conn->query("SELECT * FROM blanks WHERE location='ARCHIVE'");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="comments">Comments:</label>
        <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br><br>

        <input type="submit" value="Add Blank">
    </form>
        </div>
    </div>

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
				<th>Edit</th>
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
				echo "<td><button class='edit-row-btn'>Edit</button> <button class='delete-row-btn'>X</button></td>"; // Edit and Delete buttons for each row
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
	</div>
	
<script>
        function enableEditing(row) {
    row.addClass('editing-row');
    
    // Activate text fields for editing
    row.find('.editable').each(function() {
        var cell = $(this);
        var originalContent = cell.text();
        cell.html("<input type='text' value='" + originalContent + "' />");
    });

    // Activate dropdown fields for editing
    row.find('.editable-dropdown').each(function() {
        var cell = $(this);
        var currentValue = cell.data("value");
        var column = cell.data("column");
        var dropdown = "<select>";

        if (column === "material") {
            // Fetch materials from the database and populate the dropdown
            <?php
            $materials = $conn->query("SELECT name FROM materials");
            while ($material = $materials->fetch_assoc()) {
                echo "dropdown += '<option value=\"" . $material['name'] . "\">" . $material['name'] . "</option>';";
            }
            ?>
        } else if (column === "location") {
            dropdown += "<option value='STOCK'>STOCK</option>";
            dropdown += "<option value='MACHINE'>MACHINE</option>";
            dropdown += "<option value='ARCHIVE'>ARCHIVE</option>";
        }

        dropdown += "</select>";
        cell.html(dropdown);
        cell.find("select").val(currentValue);
    });
}

function disableEditing(row) {
    row.removeClass('editing-row');
    
    // Revert text fields to display state
    row.find('.editable').each(function() {
        var cell = $(this);
        var inputValue = cell.find('input').val();
        cell.text(inputValue);
    });

    // Revert dropdown fields to display state
    row.find('.editable-dropdown').each(function() {
        var cell = $(this);
        var selectedValue = cell.find('select').val();
        cell.data("value", selectedValue).text(selectedValue);
    });
}

    </script>
	<script>
    $(document).ready(function() {
    // Initially, disable inline editing
    $(".editable, .editable-dropdown").off('click');

    // When the Edit button is clicked
    $(".edit-row-btn").click(function() {
        var row = $(this).closest('tr');
        var btn = $(this);

        if (btn.text() === 'Edit') {
            // Enable editing for this specific row
            enableEditing(row);
            btn.text('Done');
        } else {
            // Disable editing for this specific row
            disableEditing(row);
            btn.text('Edit');
        }
    });

    // When the Delete button is clicked
    $(".delete-row-btn").click(function() {
        var row = $(this).closest('tr');
        var id = row.data("id");
        var confirmation = confirm("Delete forever?");

        if (confirmation) {
            // If the user confirms, send a request to delete the record from the database
            $.post("delete_blank.php", { id: id }, function(data) {
                if (data === "success") {
                    // Remove the row from the table
                    row.remove();
                } else {
                    alert("Error deleting record!");
                }
            });
        }
    });
});


// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("addBlankBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
$(document).ready(function() {
    $('.view-blanks-table').DataTable();
});

</script>

</body>
</html>
