<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage mit Anleitung</title>

    <!-- Bootstrap Integration -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <?php

    use src\Controllers\LoginController;

    echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/infoboxHome.css'>";
    ?>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <!-- Welcomepage on the left -->
        <div class="col-12 col-md-8">
            <h1><?php echo $welcomeMessage ?></h1>
            <p>Wir bieten Ihnen ein Tool, mit dem Sie Ihre Mietabrechnungen automatisch auf Unstimmigkeiten überprüfen können!</p>

            <?php include BASE_DIRECTORY.'src/Views/rotatingHouse.php'; ?>
        </div>

        <!-- Infobox on the right -->
        <?php
        // check if user is locked in
        $loginController = new LoginController();
        ?>
        <div class="col-12 col-md-4">
            <div class="info-box">
                <h4>Erste Schritte:</h4>
                <ol class="instruction-list">
                    <?php if (!$loginController->isLoggedIn()) { ?>
                        <li>
                            <span>1) Einloggen oder Registrieren</span>
                            <a href="index.php?page=6" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Zum Login oder Registrierung</a>
                        </li>
                        <li>
                            <span>2) Laden Sie Rechnungen für diese Immobilie hoch</span>
                            <a href="index.php?page=5" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Rechnung hochladen</a>
                        </li>
                        <li>
                            <span>3) Vergleichen Sie ihre Rechnungen</span>
                            <a href="index.php?page=13" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Rechnungen vergleichen</a>
                        </li>
                        <li>
                            <span>4) Anlaysieren Sie ihre Rechnungen</span>
                            <a hre="index.php?page=11" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Analyseergebnisse</a>
                        </li><?php
                    } else {
                        ?>
                        <li>
                            <span>1) Laden Sie Rechnungen für diese Immobilie hoch</span>
                            <a href="index.php?page=5" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Rechnung hochladen</a>
                        </li>
                        <li>
                            <span>2) Vergleichen sie ihre Rechnungen</span>
                            <a href="index.php?page=13" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Rechnungen vergleichen</a>
                        </li>
                        <li>
                            <span>3) Anlaysieren Sie ihre Rechnungen</span>
                            <a href="index.php?page=11" class="btn btn-primary btn-sm btn-action d-block w-100 mb-2">Analyseergebnisse</a>
                        </li><?php
                    } ?>
                </ol>
                <p>Viel Erfolg beim Verwenden unseres Tools!</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
