// Variable to store the selected file
let selectedFile = null;

// DOM Elements
const dragDropArea = document.getElementById('drag-drop-area'); // Area for drag-and-drop interaction
const fileInput = document.getElementById('file-input'); // Hidden file input element
const chooseFileBtn = document.getElementById('choose-file-btn'); // Button to open file explorer
const fileNameDisplay = document.getElementById('file-name'); // Element to display the selected file's name
const filePreview = document.getElementById('file-preview'); // Container for file preview
const previewImg = document.getElementById('preview-img'); // Image element to preview image files
const previewText = document.getElementById('preview-text'); // Text element for non-image file previews
const uploadBtn = document.getElementById('upload-btn'); // Button to trigger the file upload

// Drag-and-drop event handlers

// Highlight drag-drop area when file is dragged over it
dragDropArea.addEventListener('dragover', (event) => {
    event.preventDefault(); // Prevent default browser behavior (e.g., opening file)
    dragDropArea.classList.add('bg-light'); // Add visual feedback (e.g., change background color)
});

// Remove highlight when the dragged file leaves the drag-drop area
dragDropArea.addEventListener('dragleave', () => {
    dragDropArea.classList.remove('bg-light'); // Remove visual feedback
});

// Handle file drop into the drag-drop area
dragDropArea.addEventListener('drop', (event) => {
    event.preventDefault(); // Prevent default browser behavior
    dragDropArea.classList.remove('bg-light'); // Remove visual feedback

    const files = event.dataTransfer.files; // Get files from the drag event
    if (files.length > 0) {
        selectedFile = files[0]; // Store the first file as the selected file
        fileInput.files = event.dataTransfer.files; // Sync file input with dropped files
        showFile(selectedFile); // Display the file preview
    }
});

// Open the file explorer when the button is clicked
chooseFileBtn.addEventListener('click', () => {
    fileInput.click(); // Programmatically trigger the file input click event
});

// Handle file selection through the `<input>` element
fileInput.addEventListener('change', (event) => {
    if (event.target.files.length > 0) {
        selectedFile = event.target.files[0]; // Store the selected file
        showFile(selectedFile); // Display the file preview
    }
});

// Function to display a preview of the selected file
function showFile(file) {
    if (file) {
        // Update the displayed file name
        fileNameDisplay.textContent = `Selected file: ${file.name}`;
        uploadBtn.disabled = false; // Enable the upload button

        // Create a FileReader to read the file's content
        const reader = new FileReader();

        // Define what happens when the file is successfully read
        reader.onload = function (e) {
            // Check if the file is an image
            if (file.type.startsWith('image')) {
                previewImg.src = e.target.result; // Set the image preview source
                previewImg.style.display = 'block'; // Display the image element
                previewText.style.display = 'none'; // Hide the text element
            } else {
                // Display the file name for non-image files
                previewText.textContent = file.name;
                previewText.style.display = 'block'; // Show the text element
                previewImg.style.display = 'none'; // Hide the image element
            }
        };

        // Read the file as a Data URL (useful for previewing)
        reader.readAsDataURL(file);
    }
}

// Handle file upload when the upload button is clicked
uploadBtn.addEventListener('click', () => {
    // Check if a file has been selected
    if (selectedFile) {
        // Create a FormData object to package the file for upload
        const formData = new FormData();
        formData.append('file', selectedFile); // Attach the selected file to FormData

        // Optional: Send the FormData object to the server using fetch, XMLHttpRequest, or another method
        // Example:
        // fetch('/upload-endpoint', {
        //     method: 'POST',
        //     body: formData,
        // })
        // .then(response => response.json())
        // .then(data => console.log('Upload successful:', data))
        // .catch(error => console.error('Upload failed:', error));
    } else {
        // Alert the user if no file has been selected
        alert('No file selected.');
    }
});
