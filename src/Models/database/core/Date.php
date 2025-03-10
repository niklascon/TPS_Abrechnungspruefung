<?php

namespace src\Models\database\core;

use DateTime;
use Exception;

/**
 * This class makes a DateTime easier to handle and already comes up with some extra functions
 */
class Date {
    private DateTime $date;

    /**
     * Constructor to initialize the date object
     *
     * @param string|null $dateString A date string (e.g., '2024-12-01') or null for the current date
     */
    public function __construct(string $dateString = null)
    {
        if ($dateString === null) {
            $this->date = new DateTime(); // Current date and time
        } else {
            try {
                $this->date = new DateTime($dateString);
            } catch (\DateMalformedStringException $e) {
                error_log("Date Invalid: " . $e->getMessage());
            }
        }
    }

    /**
     * Get the date as a string in the database-friendly format (YYYY-MM-DD)
     *
     * @return string
     */
    public function toDatabaseFormat(): string
    {
        return $this->date->format('Y-m-d');
    }

    /**
     * Get the date as a string in a readable format (e.g., 'December 1, 2024')
     *
     * @return string
     */
    public function toReadableFormat(): string
    {
        return $this->date->format('F j, Y');
    }

    /**
     * Get the full date and time in ISO 8601 format
     *
     * @return string
     */
    public function toIsoFormat(): string
    {
        return $this->date->format(DateTime::ATOM);
    }

    /**
     * Add days to the current date
     *
     * @param int $days
     * @return void
     */
    public function addDays(int $days): void
    {
        $this->date->modify("+$days days");
    }

    /**
     * Subtract days from the current date
     *
     * @param int $days
     * @return void
     */
    public function subtractDays(int $days): void
    {
        $this->date->modify("-$days days");
    }

    /**
     * Get the DateTime object
     *
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->date;
    }

    /**
     * Set the date using a new date string
     *
     * @param string $dateString
     * @throws Exception
     */
    public function setDate(string $dateString): void
    {
        $this->date = new DateTime($dateString);
    }
}