<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\AnalysisResult;
use src\Models\database\implementation\AnalysisResults;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\ItemOfAnalysis;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\TextBracket;

class AnalysisResultsTest extends TestCase
{
    private $analysisResult;
    private $realEstate;
    private $textBracket;

    protected function setUp(): void
    {
        $this->realEstate = new RealEstate();
        $this->realEstate->setName("Test RealEstate");
        $this->realEstate->save();

        $this->textBracket = new TextBracket();
        $this->textBracket->setTextBracket("Test TestBracket");
        $this->textBracket->setShortDescription("Test Short Description");
        $this->textBracket->save();

        $this->analysisResult = new AnalysisResult();
        $this->analysisResult->setTextBracket($this->textBracket);
        $this->analysisResult->setRealEstate($this->realEstate);
        $this->analysisResult->setPriceDevelopment("big difference huh");
        $this->analysisResult->save();
    }

    public function testAddWhereRealEstate(): void
    {
        $analysisResults = new AnalysisResults();
        $analysisResults->addWhereRealEstate($this->realEstate);
        $this->assertCount(1, $analysisResults->loadAll());
    }

    public function testAddJoinBill(): void
    {
        $bill1 = new Bill();
        $bill1->setName("Test Bill");
        $bill1->setYear(2020);
        $bill1->setRealEstate($this->realEstate);
        $bill1->save();

        $lineItem = new LineItem();
        $lineItem->setBill($bill1);
        $lineItem->setPrice(10);
        $lineItem->setBookingType(new BookingType(5));
        $lineItem->setDescription("Test line item");
        $lineItem->save();

        $itemOfAnalysis = new ItemOfAnalysis();
        $itemOfAnalysis->setLineItem($lineItem);
        $itemOfAnalysis->setAnalysisResult($this->analysisResult);
        $itemOfAnalysis->save();

        $analysisResults = new AnalysisResults();
        $analysisResults->addJoinBill($bill1);
        $this->assertCount(1, $analysisResults->loadAll());

        // delete bill again
        $db = new Database();
        $db->delete("bill", $bill1->getId());
        $db->delete("line_item", $lineItem->getId());
        $db->delete("item_of_analysis", $itemOfAnalysis->getId());
    }

    protected function tearDown(): void
    {
        $db = new Database();
        $db->delete("real_estate", $this->realEstate->getId());
        $db->delete("text_bracket", $this->textBracket->getId());
        $db->delete("analysis_result", $this->analysisResult->getId());
    }
}
