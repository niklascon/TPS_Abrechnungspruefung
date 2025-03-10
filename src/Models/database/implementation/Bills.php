<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "Bill.php";
require_once "RealEstate.php";

/**
 * Manage multiple bills from the database
 */
class Bills {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * search for bills of a specific year
     *
     * @param string $year
     * @return void
     */
    public function addWhereYear(string $year): void
    {
        $this->db->addWhereClause("year = ".$year);
    }

    /**
     * search for bills of a specific real estate
     *
     * @param RealEstate $realEstate
     * @return void
     */
    public function addWhereRealEstate(RealEstate $realEstate): void
    {
        $this->db->addWhereClause("fk_real_estate = ".$realEstate->getId());
    }

    /**
     * add corresponding line items to the bill
     *
     * @return void
     */
    public function addJoinLineItem(): void
    {
        $this->db->addJoinClause("JOIN line_item ON line_item.fk_bill = bill.id");
    }

    /**
     * add corresponding join so that you get only the bills corresponding to a certain user
     *
     * @param string $userName
     * @return void
     */
    public function addJoinUser(string $userName): void
    {
        $this->db->addJoinClause("JOIN real_estate ON bill.fk_real_estate = real_estate.id");
        $this->db->addJoinClause("JOIN user_real_estate ON user_real_estate.fk_real_estate=real_estate.id");
        $this->db->addJoinClause("JOIN user ON user_real_estate.fk_user = user.id");
        $this->db->addWhereClause("user.username = '" . $userName . "'");
    }

    /**
     * Load all Bills from the database
     *
     * @return array
     */
    public function loadAll() {
        $allBills = $this->db->loadAll("bill");

        $bills = [];

        // create Bill object
        foreach ($allBills as $data) {
            $bill = new Bill($data['id']);
            $bill->setName($data['name']);
            $bill->setSum($data['sum']);
            $bill->setRealEstate(new RealEstate($data['fk_real_estate']));

            $bills[] = $bill;  // Add Bill object to array
        }

        return $bills;
    }
}
