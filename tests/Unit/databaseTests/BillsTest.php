<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\Bills;
use src\Models\database\implementation\RealEstate;

class BillsTest extends TestCase
{
    private $bill1;
    private $bill2;
    private $bill3;
    private $realEstate1;
    private $realEstate2;

    protected function setUp(): void
    {
        $this->realEstate1 = new RealEstate();
        $this->realEstate1->setName("Test RealEstate");
        $this->realEstate1->save();

        $this->realEstate2 = new RealEstate();
        $this->realEstate2->setName("Another RealEstate");
        $this->realEstate2->save();

        // $this->bills = new Bills();
        $this->bill1 = new Bill();
        $this->bill1->setName("Test Bill1");
        $this->bill1->setYear(2030);
        $this->bill1->setRealEstate($this->realEstate1);
        $this->bill1->save();

        $this->bill2 = new Bill();
        $this->bill2->setName("Test Bill2");
        $this->bill2->setYear(2040);
        $this->bill2->setRealEstate($this->realEstate1);
        $this->bill2->save();

        $this->bill3 = new Bill();
        $this->bill3->setName("Test Bill2");
        $this->bill3->setYear(2050);
        $this->bill3->setRealEstate($this->realEstate2);
        $this->bill3->save();
    }

    public function testAddWhereYear(): void
    {
        $bills = new Bills();
        $bills->addWhereYear(2030);

        $billExists = null;
        $billNotExists = null;
        foreach ($bills->loadAll() as $bill) {
            if ($bill->getId() == $this->bill1->getId()) {
                $billExists = $bill;
            }
            if ($bill->getId() == $this->bill2->getId()) {
                $billNotExists = $bill;
            }
        }
        $this->assertEquals($this->bill1->getId(), $billExists->getId());
        $this->assertNull($billNotExists);
    }

    public function testAddWhereRealEstate(): void
    {
        $bills = new Bills();
        $bills->addWhereRealEstate($this->realEstate1);

        $billExists = null;
        $billNotExists = null;
        foreach ($bills->loadAll() as $bill) {
            if ($bill->getId() == $this->bill1->getId()) {
                $billExists = $bill;
            }
            if ($bill->getId() == $this->bill3->getId()) {
                $billNotExists = $bill;
            }
        }
        $this->assertEquals($this->bill1->getId(), $billExists->getId());
        $this->assertNull($billNotExists);
    }

    public function testLoadAll(): void
    {
        $bills = new Bills();
        $allBills = $bills->loadAll();

        $this->assertIsArray($allBills);
        // $this->assertGreaterThan(0, count($allBills));

        // check if list is an object of Bill
        foreach ($allBills as $bill) {
            $this->assertInstanceOf(Bill::class, $bill);
        }
    }

    protected function tearDown(): void
    {
        // delete bill again
        $db = new Database();
        $db->delete("bill", $this->bill1->getId());
        $db->delete("bill", $this->bill2->getId());
        $db->delete("bill", $this->bill3->getId());
        $db->delete("real_estate", $this->realEstate1->getId());
        $db->delete("real_estate", $this->realEstate2->getId());
    }
}