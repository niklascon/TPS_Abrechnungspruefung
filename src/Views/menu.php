<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php
    echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/aktivMenu.css'>";
    ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Button for mobile view to toggle the navigation menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Section containing the navigation items, collapsible on smaller screens -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Menu item for the Home page -->
                <li class="nav-item <?php echo (!isset($_GET['page']) || $_GET['page'] == 0) ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php?page=0">Home</a>
                </li>
                <!-- Menu item for the Uploads page -->
                <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 2) ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php?page=2">Hochladen</a>
                </li>
                <!-- Menu item for the Exam Results page -->
                <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 11) ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php?page=11">Prüfungsergebnisse</a>
                </li>
                <!-- Menu item for the Expert Analysis page -->
                <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 13) ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php?page=13">Expertenanalyse</a>
                </li>
                <!-- Menu item for the Invoices page -->
                <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 5) ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php?page=5">Rechnungen</a>
                </li>
                <!-- Menu item for the Real Estate page -->
                <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 8) ? 'active' : ''; ?>">
                    <a class="nav-link" href="index.php?page=8">Immobilien</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <!-- Example of an account link -->
                <li><a class="btn btn-light" href="index.php?page=6"><?php echo htmlspecialchars($accountText); ?></a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Include Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
