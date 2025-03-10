<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\RealEstate;

class RealEstateTest extends TestCase
{
    private $realEstate;

    protected function setUp(): void
    {
        $this->realEstate = new RealEstate();
    }

    public function testGetName(): void
    {
        $this->realEstate->setName("Wohnung erster Stock");
        $this->assertEquals("Wohnung erster Stock", $this->realEstate->getName());
    }

    public function testSaveNewRealEstate(): void
    {
        // create real estate
        $this->realEstate->setName("Wohnung erster Stock");
        $id = $this->realEstate->save();

        // load real estate from database to compare it
        $db = new Database();
        $data = $db->load("real_estate", $id);

        $this->assertEquals("Wohnung erster Stock", $data['name']);

        // delete entry from database again
        $db->delete("real_estate", $id);
    }

    public function testDelete(): void{
        // add new Realestate and delete it again, then assert it is not in the database
        $name = "Test";
        $this->realEstate->setName($name);
        $id = $this->realEstate->save();
        $this->realEstate->delete();
        $db = new Database();
        $realEstateList = $db->loadAll("real_estate");
        $this->assertNull($realEstateList[$id], "Real Estate should be deleted, still exists");

        // there should not be an exception if one tries to delete a not existing real estate
        $this->realEstate->delete();
    }
}
