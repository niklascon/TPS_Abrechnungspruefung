<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "AnalysisResult.php";

/**
 * Manage multiple Analysis Results from the database
 * An analysis results show all findings of a bill. It usually contains around one or two line items of different bills
 * and is linked to a real estate
 */
class AnalysisResults {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * needed to get text brackets of specific real estate
     *
     * @param RealEstate $realEstate
     * @return void
     */
    public function addWhereRealEstate(RealEstate $realEstate): void
    {
        $this->db->addWhereClause("fk_real_estate = ".$realEstate->getId());
    }

    /**
     * only show analysis results of specific bill
     *
     * @param Bill $bill
     * @return void
     */
    public function addJoinBill(Bill $bill): void
    {
        $this->db->addJoinClause("JOIN item_of_analysis ON item_of_analysis.fk_analysis = analysis_result.id");
        $this->db->addJoinClause("JOIN line_item ON item_of_analysis.fk_line_item = line_item.id");
        // $this->db->addJoinClause("JOIN bill ON line_item.fk_bill = bill.id");
        $this->db->addWhereClause("line_item.fk_bill = ".$bill->getId());
    }

    /**
     * Load all Analysis Results from the database
     *
     * @return array
     */
    public function loadAll(): array
    {
        $allAnalysisResults = $this->db->loadAll("analysis_result");

        $analysisResults = [];

        // create Analysis Result object
        foreach ($allAnalysisResults as $data) {
            $analysisResult = new AnalysisResult($data['id']);
            $analysisResult->setPriceDevelopment($data['price_development']);
            $analysisResult->setRealEstate(new RealEstate($data['fk_real_estate']));
            $analysisResult->setTextBracket(new TextBracket($data['fk_text_bracket']));

            $analysisResults[] = $analysisResult;  // Add Analysis Result object to array
        }

        return $analysisResults;
    }
}
