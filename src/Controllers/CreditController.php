<?php

namespace src\Controllers;

/**
 * This Controller shows the credits to other projects that we used in ours
 */
class CreditController
{
    /**
     * show credit view
     *
     * @return void
     */
    function credit(): void
    {
        require BASE_DIRECTORY.'src/Views/credits.php';
    }
}