<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";

/**
 * Get a specific Text Bracket form the database or create a new one
 * The TextBracket indicates what is wrong about a particular line item.
 */
Class TextBracket {

    private int|null $id;
    private string $textBracket;
    private bool $textBracketLoaded = false;
    private string $shortDescription;
    private bool $shortDescriptionLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * this returns the actual text of a TextBracket object
     *
     * @return string
     */
    public function getTextBracket(): string
    {
        if (!$this->textBracketLoaded) {
            $this->load();
        }
        return $this->textBracket;
    }

    /**
     * to set the actual text of a TextBracket object
     *
     * @param $textBracket string of actual text
     * @return void
     */
    public function setTextBracket(string $textBracket): void
    {
        $this->textBracket = $textBracket;
        $this->textBracketLoaded = true;
    }

    /**
     * this returns a short description of the TextBracket object
     *
     * @return string
     */
    public function getShortDescription(): string
    {
        if (!$this->shortDescriptionLoaded) {
            $this->load();
        }
        return $this->shortDescription;
    }

    /**
     * to set a short description of a TextBracket object
     *
     * @param string $shortDescription
     * @return void
     */
    public function setShortDescription(string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
        $this->shortDescriptionLoaded = true;
    }

    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the text_bracket with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        if (is_null($this->id))
            return;

        $db = new Database();
        $data = $db->load("text_bracket", $this->id);

        if ($data) {
            $this->setTextBracket($data['text_bracket']);
            $this->setShortDescription($data['short_description']);
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
        $db = new Database();
        $this->id = $db->save("text_bracket", array(
            "id" => $this->getId(),
            "text_bracket" => $this->getTextBracket(),
            "short_description" => $this->getShortDescription()
        ));

        return $this->id;
    }

}


