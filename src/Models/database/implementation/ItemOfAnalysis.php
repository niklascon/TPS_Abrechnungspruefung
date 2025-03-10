<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "LineItem.php";
require_once "AnalysisResult.php";

/**
 * Get a specific analysis item from the database or create a new one
 * item_of_analysis (an analysis item) is a table that connects analysis results to a specific line item of a bill
 * in some cases (e.g for a pre year comparison) an analysis result is connected to two line items.
 */
Class ItemOfAnalysis {

    private int|null $id;
    private LineItem $lineItem;
    private bool $lineItemLoaded = false;
    private AnalysisResult  $analysisResult;
    private bool $analysisResultLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * get the line item that is connected to the given analysis result
     *
     * @return LineItem
     */
    public function getLineItem(): LineItem
    {
        if (!$this->lineItemLoaded) {
            $this->load();
        }
        return $this->lineItem;
    }

    /**
     * set line item of a analysis result
     *
     * @param LineItem $lineItem
     * @return void
     */
    public function setLineItem(LineItem $lineItem): void
    {
        $this->lineItem = $lineItem;
        $this->lineItemLoaded = true;
    }

    /**
     * get the analysis result that is related to a line item
     *
     * @return AnalysisResult
     */
    public function getAnalysisResult(): AnalysisResult
    {
        if (!$this->analysisResultLoaded) {
            $this->load();
        }
        return $this->analysisResult;
    }

    /**
     * set the analysis result that is related to a line item
     *
     * @param AnalysisResult $analysisResult
     * @return void
     */
    public function setAnalysisResult(AnalysisResult $analysisResult): void
    {
        $this->analysisResult = $analysisResult;
        $this->analysisResultLoaded = true;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the real_estate with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if(is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("item_of_analysis", $this->id);

        if ($data) {
            $this->setLineItem(new LineItem($data['fk_line_item']));
            $this->setAnalysisResult(new AnalysisResult($data['fk_analysis']));
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int of created entry
     */
    public function save(): int {
        $db = new Database();
        $this->id = $db->save("item_of_analysis", array(
            "id" => $this->getId(),
            "fk_line_item" => $this->getLineItem()->getId(),
            "fk_analysis" => $this->getAnalysisResult()->getId()
        ));

        return $this->id;
    }

    /**
     * delete item of analysis
     *
     * @return void
     */
    public function delete(): void {
        $db = new Database();
        $db->delete("item_of_analysis", $this->id);
    }

}


