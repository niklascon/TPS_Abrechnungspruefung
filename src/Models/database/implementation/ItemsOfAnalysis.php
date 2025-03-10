<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "ItemOfAnalysis.php";

/**
 * Manage multiple items of analysis from the database
 * item_of_analysis (an analysis item) is a table that connects analysis results to a specific line item of a bill
 * in some cases (e.g for a pre year comparison) an analysis result is connected to two line items.
 */
class ItemsOfAnalysis {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * to look for line items of a specific analysis result
     *
     * @param AnalysisResult $analysisResult
     * @return void
     */
    public function addWhereAnalysis(AnalysisResult $analysisResult): void
    {
        $this->db->addWhereClause("fk_analysis = ".$analysisResult->getId());
    }

    /**
     * to look for analysis of a specific line item
     *
     * @param LineItem $lineItem
     * @return void
     */
    public function addWhereLineItem(LineItem $lineItem): void
    {
        $this->db->addWhereClause("fk_line_item = ".$lineItem->getId());
    }

    /**
     * Load all items of analysis from the database
     *
     * @return array ItemOfAnalysis
     */
    public function loadAll(): array
    {
        $allDatas = $this->db->loadAll("item_of_analysis");

        $items = [];

        // create User object
        foreach ($allDatas as $data) {
            $item = new ItemOfAnalysis($data['id']);
            $item->setLineItem(new LineItem($data['fk_line_item']));
            $item->setAnalysisResult(new AnalysisResult($data['fk_analysis']));

            $items[] = $item;  // Add Item object to array
        }

        return $items;
    }
}
