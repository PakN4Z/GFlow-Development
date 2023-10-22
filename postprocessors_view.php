<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Edit Postprocessors</title>
    <link rel="stylesheet" href="style/gflow-style.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
	<script>
$(document).ready(function() {
    $('.view-postprocessors-table').DataTable();
});
</script>
</head>
<body>
    <h2>All Postprocessors</h2>
    <button id="addPostprocessorBtn">Add Postprocessor</button>
    
   <!-- Modal for adding a new postprocessor -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Add New Postprocessor</h3>
            <form id="postprocessorForm" action="save_postprocessor.php" method="post">
                <label for="type">Type:</label>
                <input type="text" id="type" name="type" value="hypdent" required><br><br>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required><br><br>
                <label for="ip">IP:</label>
                <input type="text" id="ip" name="ip" value="192.168.1.1" oninput="updatePingValue()"><br><br>
                <label for="ping">PING:</label>
                <input type="text" id="ping" name="ping" value="192.168.1.1" readonly><br><br>
                <input type="submit" value="Add Postprocessor">
            </form>
        </div>
    </div>

    <!-- Table to display the postprocessors -->
    <div class="table-responsive">
        <table class="view-postprocessors-table" border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>IP</th>
                    <th>PING</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db_connection.php';
                $result = $conn->query("SELECT * FROM postprocessors");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . $row['id'] . "'>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td class='editable' data-column='type'>" . $row['type'] . "</td>";
                    echo "<td class='editable' data-column='description'>" . $row['description'] . "</td>";
                    echo "<td class='editable' data-column='ip'>" . $row['ip'] . "</td>";
                    echo "<td>" . $row['ping'] . "</td>";
                    echo "<td><button class='edit-row-btn'>Edit</button> <button class='delete-row-btn'>X</button></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

<script src="postprocessors_script.js"></script>

<script>
    function updatePingValue() {
        document.getElementById('ping').value = document.getElementById('ip').value;
    }
</script>

</body>
</html>
