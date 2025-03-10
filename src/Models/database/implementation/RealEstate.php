<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once __DIR__ . '/../../../../config/paths.php';
require_once DATABASE_DIRECTORY."core/Database.php";

/**
 * Get a specific Real Estate from the database
 */
Class RealEstate {

    private int|null $id;
    private string $name;
    private bool $nameLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * get the name of the real estate
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
     * set the name of the real estate
     *
     * @param $name string you want to set
     * @return void
     */
    public function setName($name): void
    {
        $this->name = $name;
        $this->nameLoaded = true;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the real_estate with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if (is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("real_estate", $this->id);

        if ($data) {
            $this->setName($data['name']);
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int id of created or updated database entry
     */
    public function save(): int
    {
        $db = new Database();
        $this->id = $db->save("real_estate", array(
            "id" => $this->getId(),
            "name" => $this->getName()
        ));

        return $this->id;
    }

    /**
     * deletes this real estate from the database
     *
     * @return void
     */
    public function delete(): void
    {
        $db = new Database();
        $db->delete("real_estate", $this->id);
    }
}


