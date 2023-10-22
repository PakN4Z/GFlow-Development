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
	<script>
$(document).ready(function() {
    $('.view-materials-table').DataTable();
});

</script>
</head>
<body>
    <h2>All Materials</h2>
    <button id="addMaterialBtn">Add Material</button>
    
    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add new Material</h3>
        
        <form id="materialForm" action="save_material.php" method="post">
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


    <div class="table-responsive">
			<table class="view-materials-table" border="1">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
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

       

        dropdown += "</select>";
        cell.html(dropdown);
        cell.find("select").val(currentValue);
    });
}

function disableEditing(row) {
    row.removeClass('editing-row');
    
    // Data to be sent to the server
    var data = {
        id: row.data("id")
    };
    
    // Revert text fields to display state and collect data
    row.find('.editable').each(function() {
        var cell = $(this);
        var inputValue = cell.find('input').val();
        var column = cell.data("column");
        data[column] = inputValue;
        cell.text(inputValue);
    });

    // Revert dropdown fields to display state and collect data
    row.find('.editable-dropdown').each(function() {
        var cell = $(this);
        var selectedValue = cell.find('select').val();
        var column = cell.data("column");
        data[column] = selectedValue;
        cell.data("value", selectedValue).text(selectedValue);
    });

    // Send the data to the server
	console.log(data);
    $.post("update_material.php", data, function(response) {
        if (response !== "success") {
            alert("Error updating record!");
        }
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
            $.post("delete_material.php", { id: id }, function(data) {
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

$(document).ready(function() {
    $('#materialForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Serialize the form data for submission
        var formData = $(this).serialize();

        // Submit the form data using AJAX
        $.post("save_material.php", formData, function(response) {
            // Check the response from the server (you might need to adjust this based on your server's response)
            if (response.trim() === "New material added successfully!") {
                alert("New material was added");
                
                // Close the modal
                modal.style.display = "none";
                
                // Reload the page to refresh the table
                location.reload();
                
            } else {
                alert("Error adding material: " + response);
            }
        });
    });
});



// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("addMaterialBtn"); // <-- This line was changed

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



</script>

</body>
</html>
