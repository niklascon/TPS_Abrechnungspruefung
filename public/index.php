<?php

use src\Controllers\PageController;

/**
 * this is the start page
 * it forwards the user to the PageController
 * depending on the page variable in the GET the user gets forwarded to a specific side
 */

require '../src/Controllers/PageController.php';

if (!empty($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 0;
}
$controller = new PageController();
$controller->showPage($page);

