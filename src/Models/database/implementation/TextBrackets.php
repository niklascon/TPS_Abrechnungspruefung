<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "TextBracket.php";

/**
 * Manage multiple text brackets from the database
 * The TextBracket indicates what is wrong about a particular line item.
 */
class TextBrackets {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Load all Text Brackets from the database
     *
     * @return array
     */
    public function loadAll(): array
    {
        $allTextBrackets = $this->db->loadAll("text_bracket");

        $textBrackets = [];

        // create Text Bracket object
        foreach ($allTextBrackets as $data) {
            $textBracket = new TextBracket($data['id']);
            $textBracket->setTextBracket($data['text_bracket']);
            $textBracket->setShortDescription($data['short_description']);

            $textBrackets[] = $textBracket;  // Add Text Bracket object to array
        }

        return $textBrackets;
    }
}
