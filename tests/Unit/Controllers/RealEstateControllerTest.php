<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use src\Controllers\LoginController;
use src\Controllers\RealEstateController;
use src\Models\database\core\Database;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\RealEstates;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\Bills;
use src\Models\database\implementation\User;
use src\Models\database\implementation\UserRealestate;

class RealEstateControllerTest extends TestCase
{
    private $realEstateName1 = "RealEstate1";
    private $realEstateName2 = "RealEstate2";
    private $realEstate1;
    private $realEstate2;
    private $username = "username";
    private $user;
    private $userRealestate1;

    protected function setUp(): void
    {
        // define BASE_DIRECTORY
        if (!defined('BASE_DIRECTORY')) {
            define('BASE_DIRECTORY', dirname(__DIR__, 3) . DIRECTORY_SEPARATOR);
        }

        // create two real estates with different names
        $this->realEstate1 = new RealEstate();
        $this->realEstate1->setName($this->realEstateName1);
        $this->realEstate1->save();
        $this->realEstate2 = new RealEstate();
        $this->realEstate2->setName($this->realEstateName2);
        $this->realEstate2->save();

        // add user connected to realEstate1
        $this->user = new User();
        $this->user->setUsername($this->username);
        $this->user->save();
        $this->userRealestate1 = new UserRealEstate();
        $this->userRealestate1->setUser($this->user);
        $this->userRealestate1->setRealEstate($this->realEstate1);
        $this->userRealestate1->save();
    }

    public function testAddRealEstates(): void
    {
        // Mock LoginController
        $loginControllerMock = $this->createMock(LoginController::class);
        $loginControllerMock->method('isLoggedIn')->willReturn(true);
        $loginControllerMock->method('loggedUsername')->willReturn($this->username);

        // Create instance of RealEstateController
        $realEstateController = new RealEstateController($loginControllerMock);

        // Simulate a POST request with a new real estate name
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $newRealEstateName = "TestRealEstate";
        $_POST['realEstateName'] = $newRealEstateName;

        // ob_start and ob_get_clean() removes html output that is given by requestAddRealEstates()
        ob_start();
        // Capture the output of the controller method
        $realEstateController->requestAddRealEstates();
        ob_get_clean();

        // Check if the real estate was successfully added
        $realEstates = new RealEstates();
        $savedRealEstates = $realEstates->loadAllWithName($newRealEstateName);
        $this->assertCount(1, $savedRealEstates, "Expected the new real estate to be added to the database.");

        // Cleanup: Delete the added real estate
        $db = new Database();
        $db->delete("real_estate", $savedRealEstates[0]->getId());
    }

    public function testRequestAddRealEstates(): void
    {
        // Mock LoginController
        $loginControllerMock = $this->createMock(LoginController::class);
        $loginControllerMock->method('isLoggedIn')->willReturn(true);
        $loginControllerMock->method('loggedUsername')->willReturn($this->username);

        // Create instance of RealEstateController
        $realEstateController = new RealEstateController($loginControllerMock);

        // Simulate a POST request
        $newRealEstateName = "TestRealEstate";
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST["realEstateName"] = $newRealEstateName;

        // ob_start and ob_get_clean() removes html output that is given by requestAddRealEstates()
        ob_start();
        // Capture the output of the controller method
        $realEstateController->requestAddRealEstates();
        ob_get_clean();

        // Check if the real estate was successfully added
        $realEstates = new RealEstates();
        $savedRealEstates = $realEstates->loadAllWithName($newRealEstateName);
        $this->assertCount(1, $savedRealEstates, "Expected the new real estate to be added to the database.");

        // Cleanup: Delete the added real estate
        $db = new Database();
        $db->delete("real_estate", $savedRealEstates[0]->getId());
    }

    public function testRealEstateExists(): void
    {
        // Mock LoginController
        $loginControllerMock = $this->createMock(LoginController::class);
        $loginControllerMock->method('loggedUsername')->willReturn($this->username);

        // Mock RealEstateController
        $realEstateControllerMock = $this->getMockBuilder(RealEstateController::class)
            ->setConstructorArgs([$loginControllerMock]) // Setzt den gemockten LoginController
            ->onlyMethods(['realEstateExists']) // Nur die Methode realEstateExists mocken
            ->getMock();

        // test realEstateExists
        $reflection = new \ReflectionClass(RealEstateController::class);
        $method = $reflection->getMethod('realEstateExists');
        $method->setAccessible(true);

        $this->assertTrue(
            $method->invoke($realEstateControllerMock, $this->realEstateName1),
            "Expected realEstateExists to return true for an existing real estate."
        );
        $this->assertFalse(
            $method->invoke($realEstateControllerMock, $this->realEstateName2),
            "Expected realEstateExists to return false for an existing real estate."
        );

    }

    public function testCreateNewRealEstate(): void
    {
        // Mock LoginController
        $loginControllerMock = $this->createMock(LoginController::class);
        $loginControllerMock->method('loggedUsername')->willReturn($this->username);

        // create instance of mocked controller
        $realEstateController = new RealEstateController($loginControllerMock);

        // name of the real estate that we wanna add
        $newRealEstateName = "NewRealEstate";

        // call createNewRealEstate
        $reflection = new \ReflectionClass(RealEstateController::class);
        $method = $reflection->getMethod('createNewRealEstate');
        $method->setAccessible(true); // needed because function is private
        $method->invoke($realEstateController, $newRealEstateName);

        // Check if real estate exists in database
        $realEstates = new RealEstates();
        $savedRealEstates = $realEstates->loadAllWithName($newRealEstateName);
        $this->assertCount(1, $savedRealEstates, "Expected exactly one real estate to be saved with the given name.");
        $savedRealEstate = $savedRealEstates[0];

        // delete real estate again
        $db = new Database();
        $db->delete("real_estate", $savedRealEstate->getId());
    }

    public function testPostFormDeletesRealEstate(): void
    {
        // Mock LoginController
        $loginControllerMock = $this->createMock(LoginController::class);
        $loginControllerMock->method('isLoggedIn')->willReturn(true);
        $loginControllerMock->method('loggedUsername')->willReturn('testUser');

        // Create instance of RealEstateController
        $realEstateController = new RealEstateController($loginControllerMock);

        // Create two real estates
        $realEstate1 = new RealEstate();
        $realEstate1->setName("TestRealEstate1");
        $realEstate1->save();

        $realEstate2 = new RealEstate();
        $realEstate2->setName("TestRealEstate2");
        $realEstate2->save();

        // Simulate a list of real estates
        $realEstatesList = [$realEstate1, $realEstate2];

        // Simulate a POST request keeping only realEstate1
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ["row-0" => "on"]; // Only first real estate is kept

        // Use reflection to access the private method
        $reflection = new \ReflectionClass(RealEstateController::class);
        $method = $reflection->getMethod('postForm');
        $method->setAccessible(true);

        // Call the private method
        $updatedRealEstatesList = $method->invoke($realEstateController, $realEstatesList);

        // Check if only one real estate remains
        $this->assertCount(1, $updatedRealEstatesList, "Expected only one real estate to remain.");
        $this->assertEquals("TestRealEstate1", $updatedRealEstatesList[0]->getName(), "Expected TestRealEstate1 to be kept.");
    }

    public function testDeleteRealestateAndBills(): void
    {
        // Mock LoginController
        $loginControllerMock = $this->createMock(LoginController::class);
        $loginControllerMock->method('isLoggedIn')->willReturn(true);
        $loginControllerMock->method('loggedUsername')->willReturn('testUser');

        // Create instance of RealEstateController
        $realEstateController = new RealEstateController($loginControllerMock);

        // Create a real estate
        $realEstate = new RealEstate();
        $realEstate->setName("TestRealEstateToDelete");
        $realEstate->save();

        // Create a bill associated with the real estate
        $bill = new Bill();
        $bill->setRealEstate($realEstate);
        $bill->save();

        // Ensure real estate and bill exist
        $this->assertNotNull($realEstate->getId(), "Expected the real estate to exist before deletion.");
        $this->assertNotNull($bill->getId(), "Expected the bill to exist before deletion.");

        // Use reflection to access the private method
        $reflection = new \ReflectionClass(RealEstateController::class);
        $method = $reflection->getMethod('deleteRealestateAndBills');
        $method->setAccessible(true);

        // Call the private method
        $method->invoke($realEstateController, $realEstate);

        $bills = new Bills();
        $bills->addWhereRealEstate($realEstate);
        $this->assertCount(0, $bills->loadAll(), "Expected the bills to be deleted.");
    }

    protected function tearDown(): void
    {
        $db = new Database();
        $db->delete("real_estate", $this->realEstate1->getId());
        $db->delete("real_estate", $this->realEstate2->getId());
        $db->delete("user", $this->user->getId());
        $db->delete("user_real_estate", $this->userRealestate1->getId());
    }
}