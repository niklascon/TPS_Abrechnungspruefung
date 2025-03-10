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
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\TextBracket;

class AnalysisResultTest extends TestCase
{
    private $analysisResult;

    protected function setUp(): void
    {
        $this->analysisResult = new AnalysisResult();
    }

    public function testGetPriceDevelopment(): void
    {
        $this->analysisResult->setPriceDevelopment("Lorem Ipsum");
        $this->assertEquals("Lorem Ipsum", $this->analysisResult->getPriceDevelopment());
    }

    public function testGetRealestate(): void
    {
        $this->analysisResult->setRealestate(new Realestate(1));
        $this->assertEquals(1, $this->analysisResult->getRealestate()->getId());
    }

    public function testGetTextBracket(): void
    {
        $this->analysisResult->setTextBracket(new TextBracket(1));
        $this->assertEquals(1, $this->analysisResult->getTextBracket()->getId());
    }

    public function testDelete(): void
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
        $analysisResult->delete();
        $itemsOfAnalysis = new ItemsOfAnalysis();
        $itemsOfAnalysis->addWhereAnalysis($analysisResult);
        $this->assertCount(0, $itemsOfAnalysis->loadAll());
        $analysisResults = new AnalysisResults();
        $analysisResults->addJoinBill($bill);
        $this->assertCount(0, $analysisResults->loadAll());

        $db = new Database();
        $db->delete("line_item", $lineItem->getId());
        $db->delete("real_estate", $realEstate->getId());
        $db->delete("bill", $bill->getId());
    }
}
