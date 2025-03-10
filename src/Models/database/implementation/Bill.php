<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;
use src\Models\database\core\Date;

require_once DATABASE_DIRECTORY.'core/Database.php';
require_once DATABASE_DIRECTORY.'core/Date.php';
require_once 'User.php';
require_once 'RealEstate.php';
require_once 'AnalysisResults.php';
require_once 'UserRealestates.php';

/**
 * Get a specific Bill form the database or create a new one
 */
Class Bill {

    private int|null $id = null;
    private string $name = '';
    private bool $nameLoaded = false;
    private $sum;
    private $sumLoaded = false;
    private int $year = 0;
    private bool $yearLoaded = false;
    private $createTime;
    private bool $createTimeLoaded = false;
    private RealEstate $realEstate;
    private bool $realEstateLoaded = false;


    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * get the name of the bill
     *
     * @return string name
     */
    public function getName(): string
    {
        if (!$this->nameLoaded) {
            $this->load();
        }
        return $this->name;
    }

    /**
     * set the name of a bill
     *
     * @param string $name of bill
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->nameLoaded = true;
    }

    public function setSum($sum) {
        $this->sum = $sum;
        $this->sumLoaded = true;
    }

    public function getSum() {
        if (!$this->sumLoaded) {
            $this->load();
        }
        return $this->sum;
    }

    /**
     * get the year of the bill
     *
     * @return int year
     */
    public function getYear(): int
    {
        if (!$this->yearLoaded) {
            $this->load();
        }
        return $this->year;
    }

    /**
     * set the year of a bill
     *
     * @param int $year
     * @return void
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
        $this->yearLoaded = true;
    }

    /**
     * get the time when the bill was added to the database
     *
     * @return mixed
     */
    public function getCreateTime() {
        if (!$this->createTimeLoaded) {
            $this->load();
        }
        return $this->createTime;
    }

    /**
     * set the time when the bill will be added to the database
     *
     * @param $createTime
     * @return void
     */
    public function setCreateTime($createTime): void
    {
        $this->createTime = $createTime;
        $this->createTimeLoaded = true;
    }

    /**
     * get the real estate to that the bill is linked
     *
     * @return RealEstate
     */
    public function getRealEstate(): RealEstate
    {
        if (!$this->realEstateLoaded) {
            $this->load();
        }
        return $this->realEstate;
    }

    /**
     * set the real estate to that the bill is linked
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
     * returns true if bill has analysis results
     *
     * @return bool
     */
    public function hasAnalysisResult(): bool
    {
        // go through each analysis results
        $analysisResults = new AnalysisResults();
        $analysisResults->addJoinBill($this);

        if ($analysisResults->loadAll()){
            return true;
        }

        return false;
    }

    /**
     * check if the user has the permission to view this bill
     *
     * @param User $user
     * @return bool
     */
    public function hasUserPermission(User $user): bool
    {
        $userRealestates = new UserRealestates();
        $userRealestates->addWhereRealestate($this->getRealEstate());
        foreach ($userRealestates->loadAll() as $userRealestate){
            if ($userRealestate->getUser()->getId() == $user->getId()){
                return true;
            }
        }

        return false;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the bill with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if (is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("bill", $this->id);

        if ($data) {
            $this->setName($data['name']);
            $this->setSum($data['sum']);
            $this->setYear($data['year']);
            $this->setRealEstate(new RealEstate($data['fk_real_estate']));
            $this->setCreateTime(new Date($data['create_time']));
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int id of created entry
     */
    public function save(): int {
        // check if create time is null if yes than set to current time
        if ($this->getCreateTime() === null) {
            $this->setCreateTime(new Date());
        }

        $db = new Database();
        $data = array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "sum" => $this->getSum(),
            "year" => $this->getYear(),
            "fk_real_estate" => $this->getRealEstate()->getId(),
            "create_time" => $this->getCreateTime()->toDatabaseFormat()
        );

        $this->id = $db->save("bill", $data);

        return $this->id;
    }

    /**
     * deletes this bill from the database
     * and all corresponding line items, analysis results and item of analysis
     *
     * @return void
     */
    public function delete(): void
    {
        // delete analysis result and item of analysis
        $analysisResults = new AnalysisResults();
        $analysisResults->addJoinBill($this);
        foreach ($analysisResults->loadAll() as $analysisResult){
            $analysisResult->delete(); // this also deletes item of analysis
        }

        // delete line items
        $lineItems = new LineItems();
        $lineItems->addWhereBill($this);
        foreach ($lineItems->loadAll() as $lineItem){
            $lineItem->delete();
        }

        // delete bill
        $db = new Database();
        $db->delete("bill", $this->id);
    }

}


