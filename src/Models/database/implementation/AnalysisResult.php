<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."/core/Database.php";
require_once "RealEstate.php";
require_once "TextBracket.php";

/**
 * Get a specific analysis result form the database or create a new one
 * An analysis results show all findings of a bill. It usually contains around one or two line items of different bills
 * and is linked to a real estate
 */
Class AnalysisResult {

    private int|null $id;
    private string|null $priceDevelopment;
    private bool $priceDevelopmentLoaded = false;
    private RealEstate $realEstate;
    private bool $realEstateLoaded = false;
    private TextBracket $textBracket;
    private bool $textBracketLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * get the string of the price development
     *
     * @return string|null of price development
     */
    public function getPriceDevelopment(): ?string
    {
        if (!$this->priceDevelopmentLoaded) {
            $this->load();
        }
        return $this->priceDevelopment;
    }

    /**
     * set the string of the price development
     *
     * @param string|null $priceDevelopment
     * @return void
     */
    public function setPriceDevelopment(string|null $priceDevelopment): void
    {
        $this->priceDevelopment = $priceDevelopment;
        $this->priceDevelopmentLoaded = true;
    }

    /**
     * get the real estate of the analysis result
     *
     * @return RealEstate realEstate
     */
    public function getRealEstate(): RealEstate
    {
        if (!$this->realEstateLoaded) {
            $this->load();
        }
        return $this->realEstate;
    }

    /**
     * set the real estate of the analysis result
     *
     * @param RealEstate $realEstate
     * @return void
     */
    public function setRealEstate(RealEstate $realEstate): void
    {
        $this->realEstate = $realEstate;
        $this->realEstateLoaded = true;
    }

    /**
     * get the text bracket of the analysis result
     *
     * @return TextBracket
     */
    public function getTextBracket(): TextBracket
    {
        if (!$this->textBracketLoaded) {
            $this->load();
        }
        return $this->textBracket;
    }

    /**
     * set the string of the price development
     *
     * @param TextBracket $textBracket
     * @return void
     */
    public function setTextBracket(TextBracket $textBracket): void
    {
        $this->textBracket = $textBracket;
        $this->textBracketLoaded = true;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the analysis_result with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if(is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("analysis_result", $this->id);

        if ($data) {
            $this->setPriceDevelopment($data['price_development']);
            $this->setRealEstate(new RealEstate($data['fk_real_estate']));
            $this->setTextBracket(new TextBracket($data['fk_text_bracket']));
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int id of created entry
     */
    public function save() {
        $db = new Database();
        $this->id = $db->save("analysis_result", array(
            "id" => $this->getId(),
            "price_development" => $this->getPriceDevelopment(),
            "fk_real_estate" => $this->getRealEstate()->getId(),
            "fk_text_bracket" => $this->getTextBracket()->getId()
        ));

        return $this->id;
    }

    /**
     * delete analysis result from database
     * and all corresponding item_of_analysis
     *
     * @return void
     */
    public function delete(): void
    {
        // delete Item of Analysis
        $itemsOfAnalysis = new ItemsOfAnalysis();
        $itemsOfAnalysis->addWhereAnalysis($this);
        foreach ($itemsOfAnalysis->loadAll() as $itemOfAnalysis) {
            $itemOfAnalysis->delete();
        }

        // delete analysis result
        $db = new Database();
        $db->delete("analysis_result", $this->id);
    }

}


