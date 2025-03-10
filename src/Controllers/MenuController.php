<?php

namespace src\Controllers;
/**
 * Controller in charge of creating a menu on the top of most pages
 */
class MenuController
{
    /**
     * @return void Displays the Menu on the top of most pages
     */
    public function displayMenu():void
    {
        $accountText="";
        $loginController = new LoginController();
        if ($loginController->isLoggedIn()) {
            $accountText="Ausloggen";
        }else{
            $accountText="Einloggen";
        }
        require BASE_DIRECTORY. 'src/Views/menu.php';
    }
}