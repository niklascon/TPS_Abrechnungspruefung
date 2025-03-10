<!DOCTYPE html>
<?php
echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/uploadsStyle.css'>";
// Check if the file was passed
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']); // Decode the URL-encoded file name
    $filePath = 'uploads/' . basename($file); // Use only the safe file name (avoid directory traversal)
    // Check if the file exists
    if (file_exists($filePath)) {
        $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)); // Get the file extension in lowercase

        // Preview based on file type
        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            // Image preview
            echo '<img src="' . htmlspecialchars($filePath) . '" alt="Preview" style="max-width: 100%; height: auto;">';
        } elseif ($fileType === 'pdf') {
            // PDF preview
            echo '<embed src="' . htmlspecialchars($filePath) . '" type="application/pdf" width="100%" height="600px">';
        } else {
            // Unsupported file type
            echo '<p>Dieser Dateityp wird nicht unterstützt: ' . htmlspecialchars($fileType) . '</p>';
        }
    } else {
        echo '<p>Die Datei existiert nicht oder wurde nicht hochgeladen.</p>'; // File not found
    }
} else {
    echo '<p>Keine Datei zur Anzeige ausgewählt.</p>'; // No file passed
}



