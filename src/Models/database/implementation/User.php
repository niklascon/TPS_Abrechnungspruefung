<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";

/**
* Get a specific user form the database or create a new one
*/
Class User {

    private $id;
    private $username;
    private $nameLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        if (!$this->nameLoaded) {
            $this->load();
        }
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        $this->nameLoaded = true;
    }

    /**
     * this functions returns all real estates of the user
     *
     * @return array RealEstate
     */
    public function getRealEstatesOfUser() {
        $userRealestates = new UserRealestates();
        $userRealestates->addWhereUser($this);

        $realEstatesOfUser = array();
        foreach ($userRealestates->loadAll() as $userRealestate) {
            $realEstatesOfUser[] = $userRealestate->getRealestate();
        }

        return $realEstatesOfUser;
    }

    /**
     * load data for database
     *
     * @return void
     */
    private function load() {
        if (is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("user", $this->id);

        if ($data) {
            $this->setUsername($data['name']);
        }
    }

    /**
     * create or update existing entry
     *
     * @return int of created entry
     */
    public function save() {
        $db = new Database();
        $this->id = $db->save("user", array(
            "id" => $this->getId(),
            "username" => $this->getUsername()
        ));

        return $this->id;
    }

    /**
     * deletes this user from the database
     *
     * @return void
     */
    public function delete() {
        $db = new Database();
        $db->delete("user", $this->id);
    }

}


