<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\AnalysisResult;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\ItemOfAnalysis;
use src\Models\database\implementation\ItemsOfAnalysis;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\TextBracket;

class ItemOfAnalysisTest extends TestCase
{
    private $itemOfAnalysis;

    protected function setUp(): void
    {
        $this->itemOfAnalysis = new ItemOfAnalysis();
    }

    public function testGetLineItem(): void
    {
        $lineItem = new LineItem();
        $lineItem->setDescription("test");
        $this->itemOfAnalysis->setLineItem($lineItem);
        $this->assertEquals($lineItem, $this->itemOfAnalysis->getLineItem());
    }

    public function testGetAnalysisResult(): void
    {
        $analysisResult = new AnalysisResult();
        $analysisResult->setPriceDevelopment(100);
        $this->itemOfAnalysis->setAnalysisResult($analysisResult);
        $this->assertEquals($analysisResult, $this->itemOfAnalysis->getAnalysisResult());
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

        // now delete item of analysis
        $itemOfAnalysis->delete();
        $itemsOfAnalysis = new ItemsOfAnalysis();
        $itemsOfAnalysis->addWhereAnalysis($analysisResult);
        $this->assertCount(0, $itemsOfAnalysis->loadAll());

        $db = new Database();
        $db->delete("analysis_result", $analysisResult->getId());
        $db->delete("line_item", $lineItem->getId());
        $db->delete("real_estate", $realEstate->getId());
        $db->delete("bill", $bill->getId());
    }
}
