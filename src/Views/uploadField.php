<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datei-Upload</title>

    <?php
    // CSS einbinden
    echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/uploadSiteStyle.css'>";
    ?>
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Beschreibung auf der linken Seite -->
        <div class="col-md-4">
            <div class="info-text">
                <p class="text-muted" style="font-size: 0.9rem; line-height: 1.4;">
                    Hier können Sie Ihre Dateien hochladen. Ziehen Sie eine Datei in das rechte Feld oder klicken Sie auf "Durchsuchen", um eine Datei auszuwählen.
                </p>
            </div>
        </div>

        <!-- Form für Datei-Upload auf der rechten Seite -->
        <div class="col-md-8">
            <h3 class="text-center my-4">Datei hochladen</h3>

            <form method="post" enctype="multipart/form-data" id="upload-form">
                <!-- Drag-and-drop Bereich -->
                <div id="drag-drop-area" class="text-center p-5 border">
                    <!-- Anleitungstext -->
                    <p class="text-muted mb-3">Ziehen Sie eine Datei hierhin oder klicken Sie auf "Durchsuchen", um eine Datei auszuwählen.</p>

                    <!-- Button für Datei-Auswahl -->
                    <button type="button" class="btn btn-secondary mb-2" id="choose-file-btn">Durchsuchen</button>

                    <!-- Verstecktes File-Input -->
                    <input type="file" id="file-input" name="file" style="display: none;">

                    <!-- Dateiname anzeigen -->
                    <div id="file-name" class="text-info mt-2"></div>

                    <!-- Vorschau-Bereich -->
                    <div id="file-preview" style="display: none;" class="mt-4">
                        <p id="preview-text"></p> <!-- Vorschautext -->
                        <img id="preview-img" src="" alt="Vorschau" style="max-width: 200px; display: none;"> <!-- Bildvorschau -->
                        <canvas id="pdf-canvas" style="display: none; max-width: 200px; height: auto;"></canvas> <!-- PDF-Vorschau -->
                    </div>

                    <!-- Upload-Button -->
                    <button type="submit" class="btn btn-primary mt-4" id="upload-btn" disabled>Hochladen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript für die Datei-Funktionalität -->
<script src="<?php echo PUBLIC_DIRECTORY?>js/uploadfield.js"></script>
</body>
</html>
