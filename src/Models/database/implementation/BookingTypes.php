<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "BookingType.php";

/**
 * Manage multiple booking types from the database
 * A booking type declares the type of specific line item. For instance: heat costs or water costs
 */
class BookingTypes {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * search for booking types with a specific short name
     *
     * @param string $shortName
     * @return void
     */
    public function addWhereShortName(LineItem $lineItem) {
        $this->db->addWhereClause("short_name = ". "\"" . $lineItem->getDescription()."\"");
    }

    /**
     * Load all Booking Types from the database
     *
     * @return array
     */
    public function loadAll(): array
    {
        $allBookingTypes = $this->db->loadAll("booking_type");

        $bookingTypes = [];

        // create Booking Type object
        foreach ($allBookingTypes as $data) {
            $bookingType = new BookingType($data['id']);
            $bookingType->setShortName($data['short_name']);
            $bookingType->setDescription($data['description']);

            $bookingTypes[] = $bookingType;  // Add Booking Type object to array
        }

        return $bookingTypes;
    }
}
