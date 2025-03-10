<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use src\Models\analysis_result_generator\AnalysisResultGenerator;
use src\Models\database\core\Database;
use src\Models\database\implementation\AnalysisResults;
use src\Models\database\implementation\AnalysisResult;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\ItemsOfAnalysis;
use src\Models\database\implementation\ItemOfAnalysis;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\RealEstate;

class AnalysisResultGeneratorTest extends TestCase
{
    private $realEstate;
    private $bill1;
    private $lineItem1Bill1;
    private $lineItem2Bill1;
    private $bill2;
    private $lineItem1Bill2;
    private $lineItem2Bill2;
    private $lineItem1;
    private $lineItem2;
    private $analysisResultGenerator;

    protected function setUp(): void
    {
        // set up real estate
        $this->realEstate = new RealEstate();
        $this->realEstate->setName('Test RealEstate');
        $this->realEstate->save();

        // set up bill1
        $this->bill1 = new Bill();
        $this->bill1->setName('Test Bill 1');
        $this->bill1->setYear(2019);
        $this->bill1->setRealEstate($this->realEstate);
        $this->bill1->save();

        // set up lineItems for bill1
        $this->lineItem1Bill1 = new LineItem();
        $this->lineItem1Bill1->setBill($this->bill1);
        $this->lineItem1Bill1->setPrice(10);
        $this->lineItem1Bill1->setBookingType(new BookingType(5)); // 5: Straßenreinigung
        $this->lineItem1Bill1->setDescription("Straßenreinigung");
        $this->lineItem1Bill1->save();

        $this->lineItem2Bill1 = new LineItem();
        $this->lineItem2Bill1->setBill($this->bill1);
        $this->lineItem2Bill1->setPrice(32);
        $this->lineItem2Bill1->setBookingType(new BookingType(6)); // 6: Müllabfuhr
        $this->lineItem2Bill1->setDescription("Müllabfuhr");
        $this->lineItem2Bill1->save();

        // set up bill2
        $this->bill2 = new Bill();
        $this->bill2->setName('Test Bill 2');
        $this->bill2->setYear(2020);
        $this->bill2->setRealEstate($this->realEstate);
        $this->bill2->save();

        // set up lineItems for bill2
        $this->lineItem1Bill2 = new LineItem();
        $this->lineItem1Bill2->setBill($this->bill2);
        $this->lineItem1Bill2->setPrice(50);
        $this->lineItem1Bill2->setBookingType(new BookingType(5)); // 5: Straßenreinigung
        $this->lineItem1Bill2->setDescription("Straßenreinigung");
        $this->lineItem1Bill2->save();

        $this->lineItem2Bill2 = new LineItem();
        $this->lineItem2Bill2->setBill($this->bill2);
        $this->lineItem2Bill2->setPrice(30);
        $this->lineItem2Bill2->setBookingType(new BookingType(6)); // 6: Müllabfuhr
        $this->lineItem2Bill2->setDescription("Müllabfuhr");
        $this->lineItem2Bill2->save();

        $this->lineItem1 = new LineItem();
        $this->lineItem1->setPrice(100);
        $this->lineItem1->setBookingType(new BookingType(1));

        $this->lineItem2 = new LineItem();
        $this->lineItem2->setPrice(120);
        $this->lineItem2->setBookingType(new BookingType(1));

        $this->analysisResultGenerator = new AnalysisResultGenerator();
    }

    public function testCompareTwoYears(): void
    {
        $analysisResultGenerator = new AnalysisResultGenerator();
        $suspiciousValues = $analysisResultGenerator->compareTwoYears($this->bill1, $this->bill2, ["Standard" => 10]);

        // now save all analysisResults and itemOfAnalysis in database
        foreach ($suspiciousValues as $values){
            foreach ($values as $value){
                $value->save();
            }
        }

        $this->checkIfExists();
    }

    public function testPreYearComparison(): void
    {
        $analysisResultGenerator = new AnalysisResultGenerator();
        $analysisResultGenerator->preYearComparison($this->bill2);

        $this->checkIfExists();
    }

    public function testCompareTwoYearsDeviation(): void {
        //
        $deviationThresholds = ["Standard" => 10, "Müllabfuhr" => 5];

        $analysisResultGenerator = new AnalysisResultGenerator();
        $suspiciousValues = $analysisResultGenerator->compareTwoYears($this->bill1, $this->bill2, $deviationThresholds);

        // now save all analysisResults and itemOfAnalysis in database
        foreach ($suspiciousValues as $values){
            foreach ($values as $value){
                $value->save();
            }
        }
        $analysisResults = new AnalysisResults();
        $analysisResults->addWhereRealEstate($this->realEstate);
        $allAnalysisResults = $analysisResults->loadAll();
        // now there should be 2, as Müllabfuhr is included
        $this->assertCount(2, $allAnalysisResults);
    }

    private function checkIfExists(): void
    {
        $analysisResults = new AnalysisResults();
        $analysisResults->addWhereRealEstate($this->realEstate);
        $allAnalysisResults = $analysisResults->loadAll();
        // make sure only one analysis result for 'Straßenreinigung' exists
        $this->assertCount(1, $allAnalysisResults);

        // make sure only on items of analysis result exists for each line item of 'Straßenreinigung'
        $itemsOfAnalysis1 = new ItemsOfAnalysis();
        $itemsOfAnalysis1->addWhereAnalysis($allAnalysisResults[0]);
        $itemsOfAnalysis1->addWhereLineItem($this->lineItem1Bill1);
        $allItemsOfAnalysis1 = $itemsOfAnalysis1->loadAll();
        $this->assertCount(1, $allItemsOfAnalysis1);

        $itemsOfAnalysis2 = new ItemsOfAnalysis();
        $itemsOfAnalysis2->addWhereAnalysis($allAnalysisResults[0]);
        $itemsOfAnalysis2->addWhereLineItem($this->lineItem1Bill2);
        $allItemsOfAnalysis2 = $itemsOfAnalysis2->loadAll();
        $this->assertCount(1, $allItemsOfAnalysis2);

        // delete itemsOfAnalysis
        $db = new Database();
        $db->delete("item_of_analysis", $allItemsOfAnalysis1[0]->getId());
        $db->delete("item_of_analysis", $allItemsOfAnalysis2[0]->getId());
        $db->delete("analysis_result", $allAnalysisResults[0]->getId());
    }



    public function testSetUpAnalysisResultArray(): void
    {
        $deviation = 20;

        $method = new ReflectionMethod(AnalysisResultGenerator::class, 'setUpAnalysisResultArray');
        $method->setAccessible(true);
        $result = $method->invoke($this->analysisResultGenerator, $this->lineItem1, $this->lineItem2, $this->realEstate, $deviation);

        $this->assertInstanceOf(AnalysisResult::class, $result['analysisResult']);
        $this->assertInstanceOf(ItemOfAnalysis::class, $result['itemOfAnalysis1']);
        $this->assertInstanceOf(ItemOfAnalysis::class, $result['itemOfAnalysis2']);

        $this->assertEquals("Preis Veränderung um 20%", $result['analysisResult']->getPriceDevelopment());
    }

    public function testCompareTwoLineItems(): void
    {
        $deviationThresholds = ["Standard" => 10];

        $method = new ReflectionMethod(AnalysisResultGenerator::class, 'compareTwoLineItems');
        $method->setAccessible(true);
        $result = $method->invoke($this->analysisResultGenerator, $this->lineItem1, $this->lineItem2, $this->realEstate, $deviationThresholds);

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertInstanceOf(AnalysisResult::class, $result['analysisResult']);
    }

    public function testCompareTwoLineItemsNoDeviation(): void
    {
        $this->lineItem2->setPrice(105); // 5% deviation, below threshold
        $deviationThresholds = ["Standard" => 10];

        $method = new ReflectionMethod(AnalysisResultGenerator::class, 'compareTwoLineItems');
        $method->setAccessible(true);
        $result = $method->invoke($this->analysisResultGenerator, $this->lineItem1, $this->lineItem2, $this->realEstate, $deviationThresholds);

        $this->assertNull($result);
    }



    protected function tearDown(): void
    {
        $db = new Database();
        $db->delete("real_estate", $this->realEstate->getId());
        $db->delete("bill", $this->bill1->getId());
        $db->delete("bill", $this->bill2->getId());
        $db->delete("line_item", $this->lineItem1Bill1->getId());
        $db->delete("line_item", $this->lineItem2Bill1->getId());
        $db->delete("line_item", $this->lineItem1Bill2->getId());
        $db->delete("line_item", $this->lineItem2Bill2->getId());
    }
}
