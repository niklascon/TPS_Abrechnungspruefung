<?php

namespace src\Controllers;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\User;
use src\Models\database\implementation\UserRealestate;
use src\Models\database\implementation\Users;

include_once DATABASE_DIRECTORY.'implementation/Users.php';
include_once DATABASE_DIRECTORY.'implementation/User.php';

/**
 * Login Controller handles all user Interactions related to Login,
 * Registration and Logging-out by setting a variable in the session
 */
class LoginController
{
    private bool $testLoggedIn = false; //Used for testing: Simulate the session variable testLoggedIn
    private String $testUsername = ""; //Used for testing: Simulate the session variable username
    private string $nameErr = "";
    private string $realestateErr = "";

    private string $username = "";
    private string $realestate = "";

    /**
     * Displays a Login Form where a username can be entered which is then
     * accepted if this name exists in the database. Then a Variable in the
     * Session is set indicating the user to be logged in
     * @return void
     */
    public function showLoginPage(): void
    {
        $this->postLoginForm();
        require_once BASE_DIRECTORY . "/src/Views/loginpage.php";
    }
    /**
     * Displays a Registered Form where a username can be entered if it does
     * not exist it is added to the database
     * @return void
     */
    public function showRegisterPage(): void
    {
        $this->postRegisterForm();
        require_once BASE_DIRECTORY . "/src/Views/registerpage.php";
    }

    /**
     * logs a user out and redirects them to the main page
     * @return void
     */
    public function logout(): void{
        if ($this->logoutFromSession()){
            echo '<script type="text/javascript">';
            echo 'window.location.href= "'.PUBLIC_DIRECTORY.'index.php?page=0"';
            echo '</script>';
        }else{
            echo 'alert("Logout Failed!");';
            echo '<script type="text/javascript">';
            echo 'window.location.href= "'.PUBLIC_DIRECTORY.'index.php?page=0"';
            echo '</script>';
        }
    }

    /**
     * @return string for Errors about the name entered to the
     * login and registration form
     */
    public function getNameErr(): string
    {
        return $this->nameErr;
    }

    /**
     * @return string for Errors about the real estate name entered to the registration form
     */
    public function getRealestateErr(): string
    {
        return $this->realestateErr;
    }

    /**
     * Returns true if a User is currently logged in
     * @return bool
     */
    public function isLoggedIn(): bool{
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["loggedIn"])) {
            return $_SESSION["loggedIn"];
        }
        return false;
    }

    /**
     * Gives username of currently logged-in user
     * @return string
     */
    public function loggedUsername(): string{
        if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["username"])) {
            return $_SESSION["username"];
        }
        return "";
    }

    /**
     * Reads Data from the form on the login page, checks the username and logs in the user
     * by saving the username in the session
     * @return void
     */
    private function postLoginForm(): void{
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            if ($this->checkUsername($username)) {
                if ($this->usernameExists($username)) {
                    $this->loginToSession($username);
                    //redirect to homepage
                    echo '<script type="text/javascript">';
                    echo 'window.location.href= "'.PUBLIC_DIRECTORY.'index.php?page=0"';
                    echo '</script>';

                    $this->nameErr="";
                }else{
                    $this->nameErr = "*Benutzername existiert nicht";
                }
            }
        }
    }

    /**
     * Reads the data from the form on the registration form, checks the proposed username and
     * creates a new user
     * @return void
     */
    private function postRegisterForm(): void{
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $realEstateName = $_POST["realestate"];

            // check that username and realestate name are given and valid
            $formIsValid = true;
            if (!$this->checkUsername($username)) $formIsValid = false;
            if (!$this->checkRealestate($realEstateName)) $formIsValid = false;
            if (!$formIsValid) {
                return;
            }

            if (!$this->usernameExists($username)){
                $user = $this->createNewUser($username);
                $this->createNewRealestate($realEstateName, $user);
                $this->loginToSession($username);
                //redirect to homepage
                echo '<script type="text/javascript">';
                echo 'window.location.href= "'.PUBLIC_DIRECTORY.'index.php?page=0"';
                echo '</script>';
                $this->nameErr="";
            }else{
                $this->nameErr = "*Benutzername existiert bereits";
            }
        }
    }

    /**
     * Checks if a username is valid meaning it is not empty, only contains
     * letters and numerals and does not already exist. Attribute nameErr is set
     * accordingly
     * @param string $username
     * @return bool true if Username is valid
     */
    private function checkUsername(string $username): bool
    {
        if (empty($username)) {
            $this->nameErr = "*Benutzername kann nicht leer sein";
            return false;
        }elseif (!ctype_alnum($username)) {
            $this->nameErr = "*Benutzername kann nur Buchstaben und Ziffern enthalten";
            return false;
        }
        $this->nameErr = "";
        $this->username = $username;
        return true;
    }

    /**
     * Check if a real estate name is given. Attribute realestateErr is set accordingly
     * @param string $realEstate
     * @return bool true if real estate name is valid
     */
    private function checkRealestate(string $realEstate): bool
    {
        if (empty($realEstate)) {
            $this->realestateErr = "*Immobilienname kann nicht leer sein";
            return false;
        }
        $this->realestateErr = "";
        $this->realestate = $realEstate;
        return true;
    }

    /**
     * Checks in Database if a $username already exists
     * @param string $username
     * @return bool
     */
    private function usernameExists(string $username): bool{
        $users = new Users();
        $users->addWhereUserName($username);
        $userArray = $users->loadAll();
        return count($userArray)>0;
    }

    /**
     * Adds a new User to the database
     *
     * @param string $username
     * @return User $user of new user
     */
    private function createNewUser(string $username): User{
        $user = new User();
        $user->setUsername($username);
        $user->save();
        return $user;
    }

    /**
     * create a new real estate to the corresponding user and link in together
     *
     * @param string $realEstateName of the given real estate
     * @param User $user of new user
     * @return void
     */
    private function createNewRealestate(string $realEstateName, User $user): void{
        $realEstate = new RealEstate();
        $realEstate->setName($realEstateName);
        $realEstate->save();

        $userRealestate = new UserRealestate();
        $userRealestate->setUser($user);
        $userRealestate->setRealEstate($realEstate);
        $userRealestate->save();
    }

    /** Saves in Session that User is logged in as well as Username
     * @param string $username
     * @return void
     */
    private function loginToSession(string $username): void{
        $_SESSION["username"] = $username;
        $this->testUsername = $username;
        $_SESSION["loggedIn"] = true;
        $this->testLoggedIn = true;
    }

    /** loge
     * @return bool true if successfully logged out
     */
    private function logoutFromSession(): bool{
        $_SESSION["loggedIn"] = false;
        $this->testLoggedIn = false;
        $_SESSION["username"] = "";
        $this->testUsername = "";
        return true;
    }
}