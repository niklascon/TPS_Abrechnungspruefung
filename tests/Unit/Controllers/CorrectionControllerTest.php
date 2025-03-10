<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use src\Controllers\CorrectionController;
use src\Models\database\core\Database;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\User;
use src\Models\database\implementation\UserRealestate;
use src\Models\database\core\Form;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\Bills;

class CorrectionControllerTest extends TestCase
{
    private $controller;
    private $formMock;
    private $reflectionClass;
    private $formData;
    private $user;
    private $realEstate;
    private $userRealestate;
    private $loggedUser;

    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        require_once __DIR__ . "/../../../config/paths.php";

        $this->user = new User();
        $this->user->setUsername("testUser");
        $this->user->save();

        $this->realEstate = new RealEstate();
        $this->realEstate->setName("test");
        $this->realEstate->save();

        $this->userRealestate = new UserRealestate();
        $this->userRealestate->setUser($this->user);
        $this->userRealestate->setRealestate($this->realEstate);
        $this->userRealestate->save();

        $this->controller = new CorrectionController(new User());
        $this->formMock = $this->createMock(Form::class);
        $this->billsMock = $this->createMock(Bills::class);
        $this->reflectionClass = new ReflectionClass($this->controller);

        $bill = new Bill();
        $bill->setName("test");
        $bill->setYear(2010);

        $lineItem = new LineItem();
        $lineItem->setDescription('Betriebskosten umlagefähig');
        $lineItem->setPrice(100);
        $lineItem->setBookingType(new BookingType(3));

        $this->formData = $this->reflectionClass->getProperty('formData');
        $this->formData->setAccessible(true);
        $formDataValues = [
            'bill' => $bill,
            'real_estate' => $this->realEstate,
            'lineItems' => [$lineItem],
        ];
        $this->formData->setValue($this->controller, $formDataValues);

        $this->loggedUser = $this->reflectionClass->getProperty('loggedUser');
        $this->loggedUser->setAccessible(true);
        $this->loggedUser->setValue($this->controller, $this->user);
    }

    public function testChangeBillAttributes_ValidInput()
    {
        $_POST["data-0-0"] = "Valid Bill Name";
        $_POST["data-0-1"] = "2025";

        $this->formMock->method('isBillNameValid')->willReturn(true);
        $this->formMock->method('isYearValid')->willReturn(true);
        $this->formMock->method('isRealEstateValid')->willReturn(true);

        $method = $this->reflectionClass->getMethod('changeBillAttributes');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertFalse($result);
    }

    public function testChangeRealEstateAttributes_ValidInput()
    {
        $_POST["data-1-0"] = "123"; // valid real estate id

        $this->formMock->method('isRealEstateValid')->willReturn(true);

        $method = $this->reflectionClass->getMethod('changeRealEstateAttributes');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertFalse($result);
    }

    public function testChangeRealEstateAttributes_InvalidInput()
    {
        $_POST["data-1-0"] = "12";

        $this->formMock->method('isRealEstateValid')->willReturn(false);

        $method = $this->reflectionClass->getMethod('changeRealEstateAttributes');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertTrue($result);
    }

    public function testChangeBillAttributes_InvalidInput()
    {
        $_POST["data-0-0"] = "";
        $_POST["data-0-1"] = "abcd";

        $this->formMock->method('isBillNameValid')->willReturn(false);
        $this->formMock->method('isYearValid')->willReturn(false);
        $this->formMock->method('isRealEstateValid')->willReturn(true);

        $method = $this->reflectionClass->getMethod('changeBillAttributes');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertTrue($result);
    }

    public function testBillNotYetExisting_ValidInput()
    {
        $emptyArray = [];

        $this->billsMock->method('loadAll')->willReturn($emptyArray);

        $method = $this->reflectionClass->getMethod('billNotYetExisting');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, false, $this->billsMock);
        $this->assertFalse($result);
    }

    public function testBillNotYetExisting_InvalidInput()
    {
        $oldBill = new Bill();
        $oldBill->setName("test");
        $oldBill->setYear(2010);
        $arrayWithBill = [$oldBill];

        $this->billsMock->method('loadAll')->willReturn($arrayWithBill);

        $method = $this->reflectionClass->getMethod('billNotYetExisting');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, false, $this->billsMock);
        $this->assertTrue($result);
    }

    public function testChangeLineItemAttributes()
    {
        $_POST["data-2-0"] = "Müllabfuhr";
        $_POST["data-2-1"] = "100.00";

        $this->formMock->method('isLineItemValid')->willReturn(true);
        $this->formMock->method('isPriceValid')->willReturn(true);

        $method = $this->reflectionClass->getMethod('changeLineItemAttributes');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertFalse($result);
    }

    public function testPostFormValidInput()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST["data-0-0"] = "Valid Bill Name";
        $_POST["data-0-1"] = "2025";
        $_POST["data-1-0"] = $this->realEstate->getId();

        $this->formMock->method('isBillNameValid')->willReturn(true);
        $this->formMock->method('isYearValid')->willReturn(true);

        $method = $this->reflectionClass->getMethod('postForm');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertTrue($result);
    }

    public function testPostFormInvalidInput()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST["data-0-0"] = "Bitte eingeben";
        $_POST["data-0-1"] = "2025";

        $this->formMock->method('isBillNameValid')->willReturn(false);
        $this->formMock->method('isYearValid')->willReturn(true);
        $this->formMock->method('isRealEstateValid')->willReturn(true);

        $method = $this->reflectionClass->getMethod('postForm');
        $method->setAccessible(true);
        $result = $method->invoke($this->controller, $this->formMock, false);
        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        $db = new Database();
        $db->delete("user", $this->user->getId());
        $db->delete("real_estate", $this->realEstate->getId());
        $db->delete("user_real_estate", $this->userRealestate->getId());
    }
}

