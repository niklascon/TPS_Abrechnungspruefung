<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\BookingType;

class BookingTypeTest extends TestCase
{
    private $bookingType;

    protected function setUp(): void
    {
        $this->bookingType = new BookingType();
    }

    public function testGetShortName(): void
    {
        $this->bookingType->setShortName("very short shortName");
        $this->assertEquals("very short shortName", $this->bookingType->getShortName());
    }

    public function testGetDescription(): void
    {
        $this->bookingType->setDescription("very long description");
        $this->assertEquals("very long description", $this->bookingType->getDescription());
    }

    public function testSaveNewBookingType(): void
    {
        // create booking Type
        $this->bookingType->setShortName("very short shortName");
        $this->bookingType->setDescription("very long description");
        $id = $this->bookingType->save();

        // load booking type from database to compare it
        $db = new Database();
        $data = $db->load("booking_type", $id);

        $this->assertEquals("very short shortName", $data['short_name']);
        $this->assertEquals("very long description", $data['description']);

        // delete entry from database again
        $db->delete("booking_type", $id);
    }
}
