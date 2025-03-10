<?php
$correctEntries = $this->postForm($this->formData);
if ($correctEntries) { // all entries were correct
    header("Location: index.php?page=5");
    exit();
} // else there were still incorrect entries, so we display the preview again
?>
<div class="container mt-5">
    <div class="row">
        <!-- Editor (Form for editing content) -->
        <div class="col-md-6">
            <?php
            require BASE_DIRECTORY . '/src/Views/correctionform.php';
            ?>
        </div>

        <!-- File preview -->
        <div class="col-md-6">
            <p class="h1">Dokument</p>
            <div class="border p-3" style="min-height: 300px;">
                <?php echo $file // Display the file content with proper escaping ?>
            </div>
        </div>
    </div>

    <!-- Button below the table -->
    <div class="row mt-3">
        <div class="col text-center">
            <button class="btn btn-primary" onclick="window.location.href='<?php echo PUBLIC_DIRECTORY ?>index.php?page=2';">
                zurück <!-- Back button -->
            </button>
        </div>
    </div>
</div>


