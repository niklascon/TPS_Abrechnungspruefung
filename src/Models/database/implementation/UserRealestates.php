<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY.'core/Database.php';
require_once DATABASE_DIRECTORY.'implementation/UserRealestate.php';

/**
 * UserRealesate links users to a real estate and the other way around
 * This class handles multiple UserRealestates.
 */
class UserRealestates {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * to get all realestates of a specific user
     *
     * @param User $user
     * @return void
     */
    public function addWhereUser(User $user): void
    {
        $this->db->addWhereClause("fk_user = ".$user->getId());
    }

    /**
     * to get all users of a specific realestate
     *
     * @param RealEstate $realestate
     * @return void
     */
    public function addWhereRealestate(RealEstate $realestate): void
    {
        $this->db->addWhereClause("fk_real_estate = ".$realestate->getId());
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return array id of created or updated database entry
     */
    public function loadAll(): array {
        $allUserRealestates = $this->db->loadAll("user_real_estate");

        $userRealestates = [];

        // create User object
        foreach ($allUserRealestates as $data) {
            $userRealestate = new UserRealestate($data['id']);
            $userRealestate->setUser(new User($data['fk_user']));
            $userRealestate->setRealestate(new RealEstate($data['fk_real_estate']));

            $userRealestates[] = $userRealestate;  // Add UserRealestate object to array
        }

        return $userRealestates;
    }
}
