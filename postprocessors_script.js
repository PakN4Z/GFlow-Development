$(document).ready(function() {
    // Initialize DataTables for the postprocessors table
    $('.view-postprocessors-table').DataTable();

    // Function to enable editing for a row
    function enableEditing(row) {
        row.addClass('editing-row');
        
        // Activate text fields for editing
        row.find('.editable').each(function() {
            var cell = $(this);
            var originalContent = cell.text();
            cell.html("<input type='text' value='" + originalContent + "' />");
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
            var inputValue = cell.find('input').val();
            var column = cell.data("column");
            data[column] = inputValue;
            cell.text(inputValue);
        });

        $.post("update_postprocessor.php", data, function(response) {
            if (response !== "Postprocessor updated successfully!") {
                alert("Error updating postprocessor: " + response);
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
        var confirmation = confirm("Delete this postprocessor forever?");

        if (confirmation) {
            $.post("delete_postprocessor.php", { id: id }, function(data) {
                if (data === "Postprocessor deleted successfully!") {
                    row.remove();
                } else {
                    alert("Error deleting postprocessor!");
                }
            });
        }
    });

    // Modal handling
    var modal = document.getElementById("myModal");
    var btn = document.getElementById("addPostprocessorBtn");
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

    // AJAX submission for adding a new postprocessor
    $('#postprocessorForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.post("save_postprocessor.php", formData, function(response) {
            if (response.trim() === "New postprocessor added successfully!") {
                alert("New postprocessor was added");
                modal.style.display = "none";
                location.reload(); // Refresh the page to show the new postprocessor
            } else {
                alert("Error adding postprocessor: " + response);
            }
        });
    });
});
