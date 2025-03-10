<?php

namespace src\Models\database\core;

use src\Models\database\implementation\BookingTypes;

use src\Controllers\LoginController;
use src\Models\database\implementation\User;

/**
 * checks the form data for invalid inputs
 */
class Form
{
    /**
     * checks if a number value follows the 000.00 pattern
     * 000 and 000.0 is also allowed
     *
     * @param string $input
     * @return bool
     */
    public function isPriceValid(string $input): bool{
        return preg_match("/^\d+(\.\d{1,2})?$/", $input);
    }

    /**
     * checks if a description only contains german letters, _ and -
     *
     * @param string $input
     * @return bool
     */
    public function isDescriptionValid(string $input): bool{
        if ($input == "-- Wählen Sie --") return false;
        return preg_match('/^[a-zA-ZäöüÄÖÜß\s-]+$/', $input);
    }

    /**
     * checks if a BillName only contains german letters, \s, _ and -
     * the bill name must not be "Bitte eingeben" as this is only a placeholder
     *
     * @param string $input
     * @return bool
     */
    public function isBillNameValid(string $input): bool{
        if ($input == 'Bitte eingeben') return false; // TODO I don't think this is still needed
        return preg_match('/^[0-9a-zA-ZäöüÄÖÜß\s_-]+$/', $input);
    }

    /**
     * checks if the user owns the real estate and if it's a valid input
     *
     * @param string $input
     * @param User|null $user
     * @return bool
     */
    public function isRealEstateValid(string $input, User $user = null): bool{
        if ($input == 0) return false;

        // this checks if the user really owns the real estate. This is really important again hackers!
        if ($user != null && $user->getId()) {
            $realEstates = $user->getRealEstatesOfUser();
            foreach ($realEstates as $realEstate) {
                if ($realEstate->getId() == $input) {
                    return true; // only return true if the user owns the choosed real estate
                }
            }
        }

        return false;
    }

    /**
     * checks if a year input is done correctly, so 4 numbers
     *
     * @param string $input
     * @return bool
     */
    public function isYearValid(string $input): bool{
        return preg_match("/^\d{4}$/", $input);
    }

    /**
     * check if the input is one of the bookingTypes, which is necessary for a line item
     *
     * @param string $input
     * @return bool
     */
    public function isLineItemValid(string $input): bool{
        $bookingTypeShortNames = $this->getBookingTypeShortNames();
        if (in_array($input, $bookingTypeShortNames)) {
            return true;
        }
        return false;
    }

    /**
     * all the short names of existing bookingTypes
     *
     * @return array the shortnames of all bookingTypes
     */
    private function getBookingTypeShortNames(): array {
        $bookingTypes = new BookingTypes();
        $bookingTypes = $bookingTypes->loadAll();

        $shortNames = [];
        foreach ($bookingTypes as $bookingType) {
            $shortNames[] = $bookingType->getShortName();
        }

        return $shortNames;
    }

}