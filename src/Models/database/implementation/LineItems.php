<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "LineItem.php";

/**
 * Manage multiple line items from the database
 * A lineItem is exactly one row of a bill. It usually contains a booking type (e.g. Straßenreinigung) and a price
 */
class LineItems {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * to look for line items of a specific bill
     *
     * @param Bill $bill
     * @return void
     */
    public function addWhereBill(Bill $bill): void
    {
        $this->db->addWhereClause("fk_bill = ".$bill->getId());
    }

    /**
     * only show line items with a booking type that we want to compare with other bills
     *
     * @return void
     */
    public function addWhereComparableBookingTypes(): void
    {
        $this->db->addWhereClause("fk_booking_type > 4");
    }

    /**
     * Load all Line Items from the database
     *
     * @return array of LineItems
     */
    public function loadAll(): array
    {
        $allLineItems = $this->db->loadAll("line_item");

        $lineItems = [];

        // create Line Item object
        foreach ($allLineItems as $data) {
            $lineItem = new LineItem($data['id']);
            $lineItem->setDescription($data['description']);
            $lineItem->setPrice($data['price']);
            $lineItem->setBill(new Bill($data['fk_bill']));
            $lineItem->setBookingType(new BookingType($data['fk_booking_type']));

            $lineItems[] = $lineItem;  // Add Line Item object to array
        }

        return $lineItems;
    }
}
