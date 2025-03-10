<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Fix - Dynamisch mit Abstand</title>
    <?php
    // CSS einbinden
    echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/footerStyle.css'>";
    ?>
</head>
<body>
<!-- Dynamischer Inhalt -->
<div id="dynamic-content">
    <!-- Inhalt wird hier eingebunden -->
</div>

<!-- Dynamischer Footer -->
<footer class="footer">
    <p><footer>
        <p>&copy; 2025 Softwareentwicklung Prüfung WEG- und Mietabrechnungen |
            <a href="<?php echo PUBLIC_DIRECTORY . 'index.php?page=' . 10; ?>" class="link-primary">Credits & Lizenzen</a>
        </p>
    </footer>
    </a></p>
</footer>

<script src="<?php echo PUBLIC_DIRECTORY.'js/footer.js'?>"></script>
</body>
</html>
