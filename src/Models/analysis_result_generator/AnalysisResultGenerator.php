<?php

namespace src\Models\analysis_result_generator;

use src\Models\database\implementation\AnalysisResult;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\Bills;
use src\Models\database\implementation\ItemOfAnalysis;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\LineItems;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\TextBracket;

/**
 * This class generates various kinds of analysis results
 */
Class AnalysisResultGenerator
{
    /**
     * this class finds deviations to the last year and saves the output in the database
     *
     * @param Bill $bill that needs to be compared
     * @return void
     */
    public function preYearComparison(Bill $bill): void
    {
        // search for pre year bill
        $bills = new Bills();
        $bills->addWhereYear($bill->getYear() - 1);
        $bills->addWhereRealEstate($bill->getRealEstate());
        $allBills = $bills->loadAll();

        // check if a pre year bill exists
        if (count($allBills) == 0)
            return;
        $preYearBill = $allBills[0];

        $suspiciousValues = $this->compareTwoYears($preYearBill, $bill, ["Standard" => 10]);

        // now save all analysisResults and itemOfAnalysis in database
        foreach ($suspiciousValues as $values){
            foreach ($values as $value){
                $value->save();
            }
        }
    }


    /**
     * compare line items of two years and return the output
     *
     * @param Bill $bill1 is the older bill
     * @param Bill $bill2 is the newer bill
     * @param array $deviationThresholds the deviation thresholds corresponding to the bookingtypes
     * @return array of all analysisResults and itemOfAnalysis
     */
    public function compareTwoYears(Bill $bill1, Bill $bill2, array $deviationThresholds): array
    {
        $returnValues = [];

        // get line items of both years
        $lineItems1 = new LineItems();
        $lineItems1->addWhereBill($bill1);
        $lineItems1->addWhereComparableBookingTypes();
        $lineItems2 = new LineItems();
        $lineItems2->addWhereBill($bill2);
        $lineItems2->addWhereComparableBookingTypes();

        // go through both line items lists and compare items
        foreach ($lineItems2->loadAll() as $lineItem2) {
            foreach ($lineItems1->loadAll() as $lineItem1) {
                // compare two given line items and add analysisResult to list
                $value = $this->compareTwoLineItems($lineItem1, $lineItem2, $bill1->getRealEstate(), $deviationThresholds);
                if ($value != null) {
                    $returnValues[] = $value;
                }
            }
        }

        return $returnValues;
    }


    /**
     * compare two line items and return array filled with the analysisResult and itemOfAnalysis
     *
     * @param LineItem $lineItem1
     * @param LineItem $lineItem2
     * @param RealEstate $realEstate of the bill
     * @param $deviationThresholds
     * @return array|null
     */
    private function compareTwoLineItems(LineItem $lineItem1, LineItem $lineItem2, RealEstate $realEstate, $deviationThresholds): ?array
    {
        // check if it's the same booking type
        if ($lineItem2->getBookingType()->getId() == $lineItem1->getBookingType()->getId()) {
            $bookingType = $lineItem1->getBookingType();
            $currentPrice = $lineItem2->getPrice();
            $previousPrice = $lineItem1->getPrice();

            // make sure that we don't divide with 0
            if ($previousPrice > 0 && $currentPrice > 0) {
                // the following line doesn't care if it's an increase or decrease of 10%
                $deviation = abs($currentPrice - $previousPrice) / $previousPrice * 100;
                //$deviation = ($currentPrice - $previousPrice) / $previousPrice * 100;
                $deviation = round($deviation); // Round to the nearest whole number

                // check if user has set new deviation thresholds
                $deviationThreshold =
                    array_key_exists($bookingType->getShortName(), $deviationThresholds) ?
                        $deviationThresholds[$bookingType->getShortName()]: $deviationThresholds["Standard"];

                // check for a deviation greater than defined value (value is in percent)
                if ($deviation > $deviationThreshold) {
                    // create array fill with analysisResult and itemOfAnalysis
                    return $this->setUpAnalysisResultArray($lineItem1, $lineItem2, $realEstate, $deviation);
                }
            }
        }

        return null;
    }

    /**
     * set up an array filled with analysis results
     *
     * @param LineItem $lineItem1
     * @param LineItem $lineItem2
     * @param RealEstate $realEstate of the bill
     * @param $deviation
     * @return array
     */
    private function setUpAnalysisResultArray(LineItem $lineItem1, LineItem $lineItem2, RealEstate $realEstate, $deviation): array
    {
        $value = [];
        // create analysis result item
        $analysisResult = new AnalysisResult();
        $analysisResult->setRealEstate($realEstate);
        $analysisResult->setTextBracket(new TextBracket(1));
        $analysisResult->setPriceDevelopment("Preis Veränderung um ".$deviation."%");
        $value["analysisResult"] = $analysisResult;
        // $analysisResult->save();

        // save itemOfAnalysis for lineItem1
        $itemOfAnalysis1 = new ItemOfAnalysis();
        $itemOfAnalysis1->setLineItem($lineItem1);
        $itemOfAnalysis1->setAnalysisResult($analysisResult);
        $value["itemOfAnalysis1"] = $itemOfAnalysis1;
        // $itemOfAnalysis1->save();

        // save itemOfAnalysis for lineItem2
        $itemOfAnalysis2 = new ItemOfAnalysis();
        $itemOfAnalysis2->setLineItem($lineItem2);
        $itemOfAnalysis2->setAnalysisResult($analysisResult);
        $value["itemOfAnalysis2"] = $itemOfAnalysis2;
        // $itemOfAnalysis2->save();

        return $value;
    }
}