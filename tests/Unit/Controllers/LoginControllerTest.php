<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use src\Controllers\LoginController;
use src\Models\database\core\Database;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\RealEstates;
use src\Models\database\implementation\User;
use src\Models\database\implementation\UserRealestates;
use src\Models\database\implementation\Users;

class LoginControllerTest extends TestCase
{
    private LoginController $loginController;
    private ReflectionClass $reflectionClass;
    private ReflectionProperty $testLoggedIn;
    private ReflectionProperty $testUsername;
    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        require_once __DIR__ . "/../../../config/paths.php";

        $this->loginController = new LoginController();
        $this->reflectionClass = new ReflectionClass(LoginController::class);

        //Reflection Attributes of the Login Controller corresponding to the session variables
        $this->testLoggedIn = $this->reflectionClass->getProperty('testLoggedIn');
        $this->testLoggedIn->setAccessible(true);
        $this->testUsername = $this->reflectionClass->getProperty('testUsername');
        $this->testUsername->setAccessible(true);
    }

    public function testCheckUserName(){
        $method = $this->reflectionClass->getMethod('checkUserName');
        $method->setAccessible(true);

        $input="";
        $result=$method->invoke($this->loginController,$input);
        $this->assertFalse($result);
        $this->assertEquals("*Benutzername kann nicht leer sein", $this->loginController->getNameErr());

        $input="Max Mustermann";
        $result=$method->invoke($this->loginController,$input);
        $this->assertFalse($result);
        $this->assertEquals("*Benutzername kann nur Buchstaben und Ziffern enthalten", $this->loginController->getNameErr());


        $input="user1";
        $result=$method->invoke($this->loginController,$input);
        $this->assertTrue($result);
        $this->assertEquals("", $this->loginController->getNameErr());
    }

    public function testUsernameExists(){
        $method = $this->reflectionClass->getMethod('usernameExists');
        $method->setAccessible(true);

        $user = new User();
        $user->setUsername("user1");
        $id = $user->save();

        //test existing user
        $input="user1";
        $result=$method->invoke($this->loginController,$input);
        $this->assertTrue($result);

        //test non-existing user
        $input="user2";
        $result=$method->invoke($this->loginController,$input);
        $this->assertFalse($result);

        // delete entry from database again
        $db = new Database();
        $db->delete("user", $id);
    }

    public function testCreateNewUser(){
        $usernameExistsMethod = $this->reflectionClass->getMethod('usernameExists');
        $usernameExistsMethod->setAccessible(true);

        $addUserMethod = $this->reflectionClass->getMethod('createNewUser');
        $addUserMethod->setAccessible(true);

        $input = "user1";

        //check if user can be created
        $result = $addUserMethod->invoke($this->loginController,$input);
        $this->assertEquals($input, $result->getUsername());

        //check if username exists now
        $result = $usernameExistsMethod->invoke($this->loginController,$input);
        $this->assertTrue($result);

        $users = new Users();
        $users->deleteUserName("user1");
    }

    public function testLoginLogoutToSession(){
        $loginMethod = $this->reflectionClass->getMethod('loginToSession');
        $loginMethod->setAccessible(true);
        $logoutMethod = $this->reflectionClass->getMethod('logoutFromSession');
        $logoutMethod->setAccessible(true);

        //Test LoginToSession using the testLoggedIn and testUsername attributes of theLogin Controller
        $input="user1";
        $loginMethod->invoke($this->loginController,$input);

        $this->assertTrue($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals($input, $this->testUsername->getValue($this->loginController));

        //Test LogoutFromSession
        $logoutMethod->invoke($this->loginController);

        $this->assertFalse($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("", $this->testUsername->getValue($this->loginController));
    }

    public function testPostLoginForm(){
        //setup the used reflection methods
        $loginFormMethod = $this->reflectionClass->getMethod('postLoginForm');
        $loginFormMethod->setAccessible(true);
        $logoutMethod = $this->reflectionClass->getMethod('logout');
        $logoutMethod->setAccessible(true);

        //buffer output, so it is not printed to the terminal
        ob_start();

        //test login form with invalid input
        $_POST["username"] = "Max Mustermann";
        $loginFormMethod->invoke($this->loginController);

        $this->assertFalse($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("", $this->testUsername->getValue($this->loginController));

        //test login form with valid input

        $_POST["username"] = "MaxMustermann";
        $_SERVER["REQUEST_METHOD"] = "POST";
        $loginFormMethod->invoke($this->loginController);

        $this->assertTrue($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("MaxMustermann", $this->testUsername->getValue($this->loginController));

        //test logout method
        $logoutMethod->invoke($this->loginController);

        $this->assertFalse($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("", $this->testUsername->getValue($this->loginController));

        unset($_POST["username"]);
        //stop buffering output
        $output = ob_get_clean();
    }

    public function testPostRegisterForm(){
        //set up the used reflection methods
        $registerFormMethod = $this->reflectionClass->getMethod('postRegisterForm');
        $registerFormMethod->setAccessible(true);
        $logoutMethod = $this->reflectionClass->getMethod('logout');
        $logoutMethod->setAccessible(true);
        $usernameExistsMethod = $this->reflectionClass->getMethod('usernameExists');
        $usernameExistsMethod->setAccessible(true);

        //buffer output, so it is not printed to the terminal
        ob_start();

        //Test register form with invalid input
        $_POST["username"] = "Max Mustermann";
        $_POST["realestate"] = "test Immobilie";
        $registerFormMethod->invoke($this->loginController);

        $this->assertFalse($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("", $this->testUsername->getValue($this->loginController));

        $_POST["username"] = "test1";
        $_POST["realestate"] = "test Immobilie";
        $_SERVER["REQUEST_METHOD"] = "POST";
        $registerFormMethod->invoke($this->loginController);

        $this->assertTrue($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("test1", $this->testUsername->getValue($this->loginController));
        $this->assertTrue($usernameExistsMethod->invoke($this->loginController, "test1"));

        //test logout method
        $logoutMethod->invoke($this->loginController);

        $this->assertFalse($this->testLoggedIn->getValue($this->loginController));
        $this->assertEquals("", $this->testUsername->getValue($this->loginController));

        // delete all users, realestates and connections between them
        $users = new Users();
        $users->addWhereUserName("test1");
        foreach ($users->loadAll() as $user) {
            $userRealestates = new UserRealestates();
            $userRealestates->addWhereUser($user);
            foreach ($userRealestates->loadAll() as $userRealestate) {
                $userRealestate->getRealestate()->delete();
                $userRealestate->delete();
            }

            $user->delete();
        }

        unset($_POST["username"]);
        unset($_POST["realestate"]);

        //stop buffering output
        $output = ob_get_clean();
    }

    public function testCheckRealestate(){
        $method = $this->reflectionClass->getMethod('checkRealestate');
        $method->setAccessible(true);

        // check with empty real estate
        $input = "";
        $result = $method->invoke($this->loginController, $input);
        $this->assertFalse($result);
        $this->assertEquals("*Immobilienname kann nicht leer sein", $this->loginController->getRealestateErr());

        // test with valid real estate
        $input = "Beispiel Immobilien GmbH";
        $result = $method->invoke($this->loginController, $input);
        $this->assertTrue($result);
        $this->assertEquals("", $this->loginController->getRealestateErr());
    }

    public function testCreateNewRealestate(){
        $method = $this->reflectionClass->getMethod('createNewRealestate');
        $method->setAccessible(true);

        // create new user
        $user = new User();
        $user->setUsername("user1");
        $user->save();

        $realEstateName = "Beispiel Immobilien GmbH";
        $method->invoke($this->loginController, $realEstateName, $user);

        $realEstates = new RealEstates();
        $realEstates->addWhereName($realEstateName);
        $realEstateArray = $realEstates->loadAll();
        $this->assertGreaterThan(0, count($realEstateArray));

        // Überprüfen, ob die Verknüpfung des Benutzers mit der Immobilie korrekt erstellt wurde
        $userRealestates = new UserRealestates();
        $userRealestates->addWhereUser($user);
        $userRealestates->addWhereRealEstate($realEstateArray[0]);
        $userRealestateArray = $userRealestates->loadAll();
        $this->assertGreaterThan(0, count($userRealestateArray));

        // Cleanup - Lösche den Benutzer und die Immobilie nach dem Test
        $db = new Database();
        $db->delete("user", $user->getId());
        $db->delete("real_estate", $realEstateArray[0]->getId());
        $db->delete("user_real_estate", $userRealestateArray[0]->getId());
    }

}