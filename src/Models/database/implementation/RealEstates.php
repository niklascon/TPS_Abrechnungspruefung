<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "RealEstate.php";

/**
 * Manage multiple real estates from the database
 */
class RealEstates {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * add corresponding join so that you get only the real estates corresponding to a certain user
     *
     * @param string $userName
     * @return void
     */
    public function addJoinUser(string $userName): void
    {
        $this->db->addJoinClause("JOIN user_real_estate ON user_real_estate.fk_real_estate=real_estate.id");
        $this->db->addJoinClause("JOIN user ON user_real_estate.fk_user = user.id");
        $this->db->addWhereClause("user.username = '" . $userName . "'");
    }

    /**
     * get the real estate with a specific name
     *
     * @param string $name of the real estate
     * @return void
     */
    public function addWhereName(string $name): void
    {
        $this->db->addWhereClause("name = '" . $name . "'");
    }

    /**
     * Load all Real Estates from the database
     *
     * @return array
     */
    public function loadAll(): array
    {
        $allRealEstates = $this->db->loadAll("real_estate");

        $realEstates = [];

        // create Real Estate objects
        foreach ($allRealEstates as $data) {
            $realEstate = new RealEstate($data['id']);
            $realEstate->setName($data['name']);

            $realEstates[] = $realEstate;  // Add Real Estate object to array
        }

        return $realEstates;
    }

    /**
     * // TODO what is this method for. Create a method addWhereName($name) and then call loadAll
     * load all Real Estates with a certain name
     *
     * @param string $name real estate
     * @return array
     */
    public function loadAllWithName(string $name): array
    {
        $this->db->addWhereClause("real_estate.name = '".$name."'");
        return $this->loadAll();
    }
}
