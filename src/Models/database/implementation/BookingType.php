<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";

/**
 * Get a specific BookingType form the database or create a new one
 * A booking type declares the type of specific line item. For instance: heat costs or water costs
 */
Class BookingType {

    private int|null $id;
    private string $shortName;
    private bool $shortNameLoaded = false;
    private string|null $description;
    private bool $descriptionLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * get the short name of a specific booking type
     *
     * @return string
     */
    public function getShortName(): string
    {
        if (!$this->shortNameLoaded) {
            $this->load();
        }
        return $this->shortName;
    }

    /**
     * set the short name of a specific booking type
     *
     * @param string $shortName
     * @return void
     */
    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
        $this->shortNameLoaded = true;
    }

    /**
     * get the long description of a specific booking type
     *
     * @return string|null of description
     */
    public function getDescription(): string|null
    {
        if (!$this->descriptionLoaded) {
            $this->load();
        }
        return $this->description;
    }

    /**
     * set the long description of a booking type
     *
     * @param string|null $description of booking type
     * @return void
     */
    public function setDescription(string|null $description): void
    {
        $this->description = $description;
        $this->descriptionLoaded = true;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the booking_type with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if(is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("booking_type", $this->id);

        if ($data) {
            $this->setShortName($data['short_name']);
            $this->setDescription($data['description']);
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int of created entry
     */
    public function save(): int {
        $db = new Database();
        $this->id = $db->save("booking_type", array(
            "id" => $this->getId(),
            "short_name" => $this->getShortName(),
            "description" => $this->getDescription()
        ));

        return $this->id;
    }

}


