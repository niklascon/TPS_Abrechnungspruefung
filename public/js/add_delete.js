/**
 * this file enables to add and delete line items in the correction form of the uploaded bill
 */

// Adds a new row below the existing rows
function addRow() {
    var table = document.getElementById('dataTable').getElementsByTagName('tbody')[0];
    var rowCount = table.rows.length; // Get the current number of rows
    var row = table.insertRow(); // Insert a new row at the end of the table

    // Number of columns excluding the "Delete" column
    var colCount = 3//table.rows[0].cells.length; // Use the first row to determine the column count

    // Add new cells to the row
    for (var i = 0; i < colCount - 1; i++) { // Exclude the last column (Delete button)
        var cell = row.insertCell(i); // Create a new cell

        if (i === 0) { // First column: Dropdown menu for description
            var select = document.createElement("select"); // Create a select element for dropdown
            select.className = "form-control";
            select.name = "data-" + rowCount + "-0"; // Set a unique name for the select element
            select.required = true;

            // Add a default option while loading
            var defaultOption = document.createElement("option");
            defaultOption.value = "";
            defaultOption.textContent = "Loading...";
            select.appendChild(defaultOption);

            // Fetch booking types via AJAX
            fetch('index.php?page=15')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data loaded successfully:', data);
                    select.innerHTML = '';

                    var chooseOption = document.createElement("option");
                    chooseOption.value = "";
                    chooseOption.textContent = "-- Wählen Sie --";
                    select.appendChild(chooseOption);

                    data.forEach(function(option) {
                        var opt = document.createElement("option");
                        opt.value = option.value;
                        opt.textContent = option.text;
                        select.appendChild(opt);
                    });
                })
                .catch(error => {
                    console.error('Error loading booking types:', error);
                    select.innerHTML = '<option value="">Error loading options</option>';
                });

            cell.appendChild(select); // Append the select element to the first cell
        } else {
            var input = document.createElement("input");
            input.type = "number";
            input.className = "form-control";
            input.name = "data-" + rowCount + "-1"; // Set a unique name for the input
            input.maxLength = 64; // Set maximum length for input
            input.min = "0";
            input.step = "0.01";
            input.required = true;
            cell.appendChild(input); // Append the input element to the cell
        }
    }

    // Add the delete button in the last column
    var deleteCell = row.insertCell(colCount - 1);
    var deleteButton = document.createElement("button");
    deleteButton.type = "button";
    deleteButton.className = "btn btn-outline-danger";
    deleteButton.innerHTML = '<i class="bi bi-trash"></i> Löschen';
    deleteCell.appendChild(deleteButton);

    // Set a unique ID for the new row
    row.id = `row-${rowCount}`;
}

// remove a row when delete button is clicked
function removeRow(rowId) {
    var row = document.getElementById("row-" + rowId);
    if (row) {
        row.parentNode.removeChild(row);
    }
}

// makes the Wiederherstellen and Speichern button visible when löschen is clicked
function addSubmitBillDelete() {
    document.getElementById('reload').style.display = 'inline-block';
    document.getElementById('submit').style.display = 'inline-block';
}

// mark row that we want to delete
function markRowForDeletion(button) {
    var row = button.closest("tr");
    row.classList.toggle("marked-for-deletion"); // add class that makes a red border
}


// removes the marked rows if user clicks on submit
function confirmDeletion(event) {
    const rows = document.querySelectorAll(".marked-for-deletion");
    if (rows.length === 0) return; // return if nothing is selected but still pressed on submit somehow

    rows.forEach(row => {
        var input = row.querySelector("input");
        if (input) input.remove(); // removes input from post
    });
}