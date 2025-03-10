<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\TextBracket;

class TextBracketTest extends TestCase
{
    private $testBracket;

    protected function setUp(): void
    {
        $this->testBracket = new TextBracket();
    }

    public function testGetTextBracket(): void
    {
        $this->testBracket->setTextBracket("Hier steht ganz viel Text!");
        $this->assertEquals("Hier steht ganz viel Text!", $this->testBracket->getTextBracket());
    }

    public function testGetShortDescription(): void
    {
        $this->testBracket->setShortDescription("Das ist eine short description.");
        $this->assertEquals("Das ist eine short description.", $this->testBracket->getShortDescription());
    }

    public function testSaveNewTextBracket(): void
    {
        // create text bracket
        $this->testBracket->setTextBracket("Hier steht ganz viel Text!");
        $this->testBracket->setShortDescription("Das ist eine short description.");
        $id = $this->testBracket->save();

        // load text bracket from database to compare it
        $db = new Database();
        $data = $db->load("text_bracket", $id);

        $this->assertEquals("Hier steht ganz viel Text!", $data['text_bracket']);
        $this->assertEquals("Das ist eine short description.", $data['short_description']);

        // delete entry from database again
        $db->delete("text_bracket", $id);
    }
}
