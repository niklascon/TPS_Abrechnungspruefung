<?php

namespace src\Controllers;

/**
 * This class handles file upload functionality, including rendering the upload view
 * and processing uploaded files with validation.
 */
class UploadController
{
    /**
     * Main function to display the upload field view and handle the upload process.
     *
     * @return void
     */
    function main(): void
    {
        // check if user is logged in
        $loginController = new LoginController();

        if ($loginController->isLoggedIn()) {
            // Include the view file for the upload form.
            require BASE_DIRECTORY . 'src/Views/uploadField.php';

            // Handle the file upload process.
            $this->upload();
        } else {
            // user is not logged in, so empty page shown
            require BASE_DIRECTORY.'src/Views/showEmptyUpload.php';
        }
    }

    /**
     * Processes the uploaded file, performs validation, and stores it securely.
     *
     * @return void
     */
    function upload(): void
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Check if a file was uploaded and if it was uploaded without errors.
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                // TODO possible safety issue, because the error code is directly given
                $_SESSION['error_message'] = "Upload didn't work. Error Code: " . $_FILES['file']['error'];
                exit;
            }

            // Define the directory where uploaded files will be stored.
            $targetDir = 'uploads/';

            // Ensure the upload directory exists by creating it if necessary.
            $this->mkdir($targetDir);

            // Extract the original file name and sanitize it to prevent security issues.
            $fileName = basename($_FILES['file']['name']); // Original file name from the uploaded file.
            $safeFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9-_.]/', '_', $fileName); // Unique, safe file name.
            $safeFilePath = $targetDir . $safeFileName; // Full path to store the file.

            // Determine the file extension (lowercased) for validation.
            $fileType = strtolower(pathinfo($safeFilePath, PATHINFO_EXTENSION));

            // Validate the file type and size to ensure it meets the criteria.
            $this->checkFile($fileType, $_FILES['file']['size']);

            // Attempt to move the uploaded file to the target directory.
            if (move_uploaded_file($_FILES['file']['tmp_name'], $safeFilePath)) {
                // If successful, store a success message in the session.
                $_SESSION['success_message'] = 'The file was successfully uploaded!';

                // Redirect to a new page with a success message and the file name.
                echo '<script type="text/javascript">';
                echo 'window.location.href= "' . PUBLIC_DIRECTORY . 'index.php?page=1&file=' . urlencode($safeFileName) . '"';
                echo '</script>';
            } else {
                // If file upload fails, store an error message in the session.
                $_SESSION['error_message'] = "Couldn't upload the file. Please try again.";
            }
        }
    }

    /**
     * Creates a directory if it does not already exist.
     *
     * @param string $targetDir Path to the target directory.
     * @return void
     */
    private function mkdir(string $targetDir): void
    {
        // Check if the directory exists, and create it with appropriate permissions if not.
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
    }

    /**
     * Validates the uploaded file's type and size.
     *
     * @param string $fileType The file's extension (e.g., jpg, png).
     * @param int $fileSize The size of the file in bytes.
     * @return void
     */
    private function checkFile(string $fileType, int $fileSize): void
    {
        // Define the maximum allowed file size in bytes (5 MB).
        $maxFileSize = 5 * 1024 * 1024;

        // Define the list of allowed file extensions.
        $validFileTypes = ['jpg', 'png', 'gif', 'pdf', 'docx'];

        // Validate the file size, ensuring it does not exceed the maximum limit.
        if ($fileSize > $maxFileSize) {
            echo '<script>alert("Die hochgeladene Datei ist zu groß. Bitte stellen Sie sicher, dass sie 5 MB nicht überschreitet.");</script>';
            exit;
        }

        // Validate the file type against the allowed extensions.
        if (!in_array($fileType, $validFileTypes)) {
            echo '<script>alert("Nur die folgenden Dateitypen sind erlaubt: JPG, PNG, GIF, PDF, DOCX.");</script>';
            exit;
        }
    }
}
