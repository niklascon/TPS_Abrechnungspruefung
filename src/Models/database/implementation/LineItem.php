<?php

namespace src\Models\database\implementation;

use src\Models\database\core\Database;

require_once DATABASE_DIRECTORY."core/Database.php";
require_once "Bill.php";
require_once "BookingType.php";

/**
 * Get a specific LineItem from the database or create a new one
 * A lineItem is exactly one row of a bill. It usually contains a booking type (e.g. Straßenreinigung) and a price
 */
Class LineItem {

    private int|null $id;
    private string $description;
    private bool $descriptionLoaded = false;
    private $price;
    private bool $priceLoaded = false;
    private Bill $bill;
    private bool $billLoaded = false;
    private BookingType $bookingType;
    private bool $bookingTypeLoaded = false;

    public function __construct($id = null) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * get a short description of the line ite
     *
     * @return string description
     */
    public function getDescription(): string
    {
        if (!$this->descriptionLoaded) {
            $this->load();
        }
        return $this->description;
    }

    /**
     * set a short description of a line item
     *
     * @param $description string of line item
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
        $this->descriptionLoaded = true;
    }

    /**
     * set the price of a line item
     *
     * @param mixed $price of line item
     * @return void
     */
    public function setPrice($price): void
    {
        $this->price = $price;
        $this->priceLoaded = true;
    }

    /**
     * get the price of a line item
     *
     * @return mixed
     */
    public function getPrice() {
        if (!$this->priceLoaded) {
            $this->load();
        }
        return $this->price;
    }

    /**
     * set the bill that the line item is related to
     *
     * @param Bill $bill
     * @return void
     */
    public function setBill(Bill $bill): void
    {
        $this->bill = $bill;
        $this->billLoaded = true;
    }

    /**
     * get the bill that is related to the line item
     *
     * @return Bill
     */
    public function getBill(): Bill
    {
        if (!$this->billLoaded) {
            $this->load();
        }
        return $this->bill;
    }

    /**
     * set the booking type of line item. This says what type of line item it is
     *
     * @param BookingType $bookingType
     * @return void
     */
    public function setBookingType(BookingType $bookingType): void
    {
        $this->bookingType = $bookingType;
        $this->bookingTypeLoaded = true;
    }

    /**
     * get the booking type of line item. It says what kind of line item it is
     *
     * @return BookingType
     */
    public function getBookingType(): BookingType
    {
        if (!$this->bookingTypeLoaded) {
            $this->load();
        }
        return $this->bookingType;
    }



    /**
     * load data from database
     * it needs a specific id that is given in the constructor
     * this function enables to load all data of the line_item with the given id and stores it in this object
     *
     * @return void
     */
    private function load(): void
    {
        $db = new Database();
        $data = $db->load("line_item", $this->id);

        if ($data) {
            $this->setDescription($data['description']);
            $this->setPrice($data['price']);
            $this->setBill(new Bill($data['fk_bill']));
            $this->setBookingType(new BookingType($data['fk_booking_type']));
        }
    }

    /**
     * create or update existing entry
     * In particular with a given id it updates the database entry.
     * without an id it creates a new database item with the given attributes that were set before
     *
     * @return int of created entry
     */
    public function save(): int
    {
        $db = new Database();
        $this->id = $db->save("line_item", array(
            "id" => $this->getId(),
            "description" => $this->getDescription(),
            "price" => $this->getPrice(),
            "fk_bill" => $this->getBill()->getId(),
            "fk_booking_type" => $this->getBookingType()->getId()
        ));

        return $this->id;
    }

    /**
     * delete this line item from the database
     *
     * @return void
     */
    public function delete(): void
    {
        $db = new Database();
        $db->delete("line_item", $this->getId());
    }

}


