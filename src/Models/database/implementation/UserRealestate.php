<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "User.php";
require_once "RealEstate.php";

/**
 * UserRealesate links users to a real estate and the other way around
 * This class in particular handels exactly one UserRealestate.
 * It get's all items from the database and enables to save new ones
 */
Class UserRealestate {

    private int|null $id;
    private User $user;
    private bool $userLoaded = false;
    private RealEstate $realestate;
    private bool $realestateLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getUser(): User
    {
        if (!$this->userLoaded) {
            $this->load();
        }
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->userLoaded = true;
    }

    public function getRealestate(): Realestate
    {
        if (!$this->realestateLoaded) {
            $this->load();
        }
        return $this->realestate;
    }

    public function setRealestate(Realestate $realestate): void
    {
        $this->realestate = $realestate;
        $this->realestateLoaded = true;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the user_real_estate with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if (is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("user_real_estate", $this->id);

        if ($data) {
            $this->setUser(new User($data['fk_user']));
            $this->setRealestate(new Realestate($data['fk_real_estate']));
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int id of created or updated database entry
     */
    public function save(): int {
        $db = new Database();
        $this->id = $db->save("user_real_estate", array(
            "id" => $this->getId(),
            "fk_user" => $this->getUser()->getId(),
            "fk_real_estate" => $this->getRealestate()->getId()
        ));

        return $this->id;
    }

    /**
     * deletes this user real estate from the database
     *
     * @return void
     */
    public function delete(): void
    {
        $db = new Database();
        $db->delete("user_real_estate", $this->id);
    }
}


