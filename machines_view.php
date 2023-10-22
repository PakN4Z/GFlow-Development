<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Edit Machines</title>
    <link rel="stylesheet" href="style/gflow-style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
	<script>
$(document).ready(function() {
    $('.view-machines-table').DataTable();
});
</script>
</head>
<body>
    <h2>All Machines</h2>
    <button id="addMachineBtn">Add Machine</button>
    
    <!-- Modal for adding a new machine -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add New Machine</h3>
            <form id="machineForm" action="save_machine.php" method="post">
                <label for="name">Machine Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="machineType">Machine Type:</label>
                <select id="machineType" name="machineType">
                    <option value="Mikron HSM 400 U">Mikron HSM 400 U</option>
                </select><br><br>
                <label for="ncExtension">NC Extension:</label>
                <input type="text" id="ncExtension" name="ncExtension" value="h"><br><br>
                <label for="remoteIp">Remote IP:</label>
                <input type="text" id="remoteIp" name="remoteIp" value="192.168.1.228"><br><br>
                <label for="autoSleep">AutoSleep:</label>
                <select id="autoSleep" name="autoSleep">
                    <option value="Disabled">Disabled</option>
                    <option value="15 min">15 min</option>
                    <option value="30 min">30 min</option>
                </select><br><br>
                <input type="submit" value="Add Machine">
            </form>
        </div>
    </div>

    <!-- Table to display the machines -->
    <div class="table-responsive">
        <table class="view-machines-table" border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Machine Type</th>
                    <th>NC Extension</th>
                    <th>Remote IP</th>
                    <th>AutoSleep</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_connection.php';
                $result = $conn->query("SELECT * FROM machines");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . $row['id'] . "'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td class='editable' data-column='name'>" . $row['name'] . "</td>";
                    echo "<td class='editable' data-column='machine_type'>" . $row['machine_type'] . "</td>";
					echo "<td class='editable' data-column='nc_extension'>" . $row['nc_extension'] . "</td>";
					echo "<td class='editable' data-column='remote_ip'>" . $row['remote_ip'] . "</td>";
					echo "<td class='editable' data-column='auto_sleep'>" . $row['auto_sleep'] . "</td>";
                    echo "<td><button class='edit-row-btn'>Edit</button> <button class='delete-row-btn'>X</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
<script>
$(document).ready(function() {
    // Initialize DataTables for the machines table
    $('.view-machines-table').DataTable();

    // Function to enable editing for a row
    function enableEditing(row) {
        row.addClass('editing-row');
        
        // Activate text fields for editing
        row.find('.editable').each(function() {
            var cell = $(this);
            var originalContent = cell.text();
            var column = cell.data("column");
            
            if (column === 'auto_sleep') {
                // Create a dropdown for the auto_sleep column
                var selectHtml = '<select name="auto_sleep">';
                selectHtml += '<option value="Disabled"' + (originalContent === 'Disabled' ? ' selected' : '') + '>Disabled</option>';
                selectHtml += '<option value="15 min"' + (originalContent === '15 min' ? ' selected' : '') + '>15 min</option>';
                selectHtml += '<option value="30 min"' + (originalContent === '30 min' ? ' selected' : '') + '>30 min</option>';
                selectHtml += '</select>';
                cell.html(selectHtml);
            } else {
                cell.html("<input type='text' value='" + originalContent + "' />");
            }
        });
    }

    // Function to disable editing for a row and send updated data to the server
    function disableEditing(row) {
        row.removeClass('editing-row');
        
        var data = {
            id: row.data("id")
        };
        
        row.find('.editable').each(function() {
            var cell = $(this);
            var column = cell.data("column");
            
            if (column === 'auto_sleep') {
                var selectedValue = cell.find('select').val();
                data[column] = selectedValue;
                cell.text(selectedValue);
            } else {
                var inputValue = cell.find('input').val();
                data[column] = inputValue;
                cell.text(inputValue);
            }
        });

        $.post("update_machine.php", data, function(response) {
            if (response !== "Machine updated successfully!") {
                alert("Error updating machine: " + response);
            }
        });
    }

    // When the Edit button is clicked
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

    // When the Delete button is clicked
    $(".delete-row-btn").click(function() {
        var row = $(this).closest('tr');
        var id = row.data("id");
        var confirmation = confirm("Delete this machine forever?");

        if (confirmation) {
            $.post("delete_machine.php", { id: id }, function(data) {
                if (data === "Machine deleted successfully!") {
                    row.remove();
                } else {
                    alert("Error deleting machine!");
                }
            });
        }
    });

    // Modal handling
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("addMachineBtn");
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

    // AJAX submission for adding a new machine
    $('#machineForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.post("save_machine.php", formData, function(response) {
            if (response.trim() === "New machine added successfully!") {
                alert("New machine was added");
                modal.style.display = "none";
                location.reload(); // Refresh the page to show the new machine
            } else {
                alert("Error adding machine: " + response);
            }
        });
    });
});
</script>

</body>
</html>
