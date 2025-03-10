<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\AnalysisResult;
use src\Models\database\implementation\AnalysisResults;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\ItemOfAnalysis;
use src\Models\database\implementation\ItemsOfAnalysis;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\LineItems;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\TextBracket;


class BillTest extends TestCase
{
    private $bill;
    private $realEstate;

    protected function setUp(): void
    {
        $this->bill = new Bill();

        $this->realEstate = new RealEstate();
        $this->realEstate->setName("RealEstate");
        $this->realEstate->save();
    }

    public function testGetName(): void
    {
        $this->bill->setName("Handwerker");
        $this->assertEquals("Handwerker", $this->bill->getName());
    }

    public function testGetSum(): void
    {
        $this->bill->setSum(42);
        $this->assertEquals(42, $this->bill->getSum());
    }

    public function testGetYear(): void
    {
        $this->bill->setYear(2024);
        $this->assertEquals(2024, $this->bill->getYear());
    }

    public function testRealEstate(): void
    {
        $this->bill->setRealEstate($this->realEstate);
        $this->assertEquals($this->realEstate, $this->bill->getRealEstate());
    }

    public function testSaveNewBill(): void
    {
        $year = 2024;
        $sum = 122;
        $name = "Test name";

        $this->bill->setSum($sum);
        $this->bill->setName($name);
        $this->bill->setYear($year);
        $this->bill->setRealEstate($this->realEstate);
        $id = $this->bill->save();

        $db = new Database();
        $data = $db->load("bill", $id);

        $this->assertEquals($sum, $data['sum']);
        $this->assertEquals($name, $data["name"]);
        $this->assertEquals($year, $data["year"]);

        // delete entry from database again
        $db->delete("bill", $id);
    }

    protected function tearDown(): void
    {
        $db = new Database();
        $db->delete("real_estate", $this->realEstate->getId());
    }

    public function testDelete(): void{
        $year = 2024;
        $sum = 122;
        $name = "Test name";

        $this->bill->setSum($sum);
        $this->bill->setName($name);
        $this->bill->setYear($year);
        $this->bill->setRealEstate($this->realEstate);
        $id = $this->bill->save();

        $this->bill->delete();
        $db = new Database();
        $billList = $db->loadAll("bill");
        $this->assertNull($billList[$id]);
    }

    public function testDeleteAll(): void
    {
        // set up everything
        $realEstate = new RealEstate();
        $realEstate->setName("test");
        $realEstate->save();

        $bill = new Bill();
        $bill->setName("test");
        $bill->setYear(2020);
        $bill->setRealEstate($realEstate);
        $bill->save();

        $lineItem = new LineItem();
        $lineItem->setDescription("Lorem ipsum");
        $lineItem->setPrice(100);
        $lineItem->setBookingType(new BookingType(11));
        $lineItem->setBill($bill);
        $lineItem->save();

        $analysisResult = new AnalysisResult();
        $analysisResult->setPriceDevelopment(100);
        $analysisResult->setTextBracket(new TextBracket(1));
        $analysisResult->setRealEstate($realEstate);
        $analysisResult->save();

        $itemOfAnalysis = new ItemOfAnalysis();
        $itemOfAnalysis->setLineItem($lineItem);
        $itemOfAnalysis->setAnalysisResult($analysisResult);
        $itemOfAnalysis->save();

        // now delete analysis result
        $bill->delete();
        $lineItems = new LineItems();
        $lineItems->addWhereBill($bill);
        $this->assertCount(0, $lineItems->loadAll());
        $itemsOfAnalysis = new ItemsOfAnalysis();
        $itemsOfAnalysis->addWhereAnalysis($analysisResult);
        $this->assertCount(0, $itemsOfAnalysis->loadAll());
        $analysisResults = new AnalysisResults();
        $analysisResults->addJoinBill($bill);
        $this->assertCount(0, $analysisResults->loadAll());

        $db = new Database();
        $db->delete("real_estate", $realEstate->getId());
    }
}
