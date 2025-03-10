<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "User.php";

/**
 * Manage multiple users from the database
 */
class Users {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Load all Users from the database
     *
     * @return array
     */
    public function loadAll(): array
    {
        $allUser = $this->db->loadAll("user");

        $users = [];

        // create User object
        foreach ($allUser as $data) {
            $user = new User($data['id']);
            $user->setUsername($data['username']);

            $users[] = $user;  // Add User object to array
        }

        return $users;
    }

    /**
     * search users for a specific username
     *
     * @param $username
     * @return void
     */
    public function addWhereUserName($username): void
    {
        $this->db->addWhereClause("username = '".$username."'");
    }

    /** Deletes all users from the database with $name as username
     * @param $username
     * @return void
     */
    public function deleteUserName($username): void
    {
        $this->db->addWhereClause("username = '".$username."'");
        $usersToDelete = $this->loadAll();
        foreach ($usersToDelete as $user) {
            $this->db->delete("user", $user->getId());
        }
    }
}
