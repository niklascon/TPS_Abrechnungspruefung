<?php

namespace Unit\databaseTests;

use PHPUnit\Framework\TestCase;
use src\Models\database\core\Database;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\LineItems;
use src\Models\database\implementation\RealEstate;

class LineItemTest extends TestCase
{
    private $lineItem;

    protected function setUp(): void
    {
        $this->lineItem = new LineItem();
    }

    public function testGetDescription(): void
    {
        $this->lineItem->setDescription("Lorem ipsum");
        $this->assertEquals("Lorem ipsum", $this->lineItem->getDescription());
    }

    public function testGetPrice(): void
    {
        $this->lineItem->setPrice(100);
        $this->assertEquals(100, $this->lineItem->getPrice());
    }

    public function testGetBill(): void
    {
        $bill = new Bill();
        $bill->setName("test");
        $this->lineItem->setBill($bill);
        $this->assertEquals($bill, $this->lineItem->getBill());
    }

    public function testGetBookingType(): void
    {
        $bookingType = new BookingType();
        $bookingType->setShortName("test");
        $this->lineItem->setBookingType($bookingType);
        $this->assertEquals($bookingType, $this->lineItem->getBookingType());
    }

    public function testDelete(): void
    {
        // set up everything
        $realEstate = new RealEstate();
        $realEstate->setName("test");
        $realEstate->save();

        $bill = new Bill();
        $bill->setName("test");
        $bill->setYear(2020);
        $bill->setRealEstate($realEstate);
        $bill->save();

        $lineItem = new LineItem();
        $lineItem->setDescription("Lorem ipsum");
        $lineItem->setPrice(100);
        $lineItem->setBookingType(new BookingType(11));
        $lineItem->setBill($bill);
        $lineItem->save();

        // now delete line item
        $lineItem->delete();
        $lineItems = new LineItems();
        $lineItems->addWhereBill($bill);
        $this->assertCount(0, $lineItems->loadAll());

        $db = new Database();
        $db->delete("real_estate", $realEstate->getId());
        $db->delete("bill", $bill->getId());
    }
}
