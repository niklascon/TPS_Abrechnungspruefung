<?php

namespace src\Models\pdf_parsing;

// to be changed, path in config file
require __DIR__ . '/../../../vendor/autoload.php';

use Smalot\PdfParser\Parser;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\BookingType;
use src\Models\database\implementation\BookingTypes;
use src\Models\database\implementation\LineItem;

/**
 * parses a PDF and extracts important data
 * right now most of the code is hardcoded and only works for one specific bill type
 */
class PDFParser {
    private Parser $parser;

    /**
     * input optional parser. This is need for testing
     *
     * @param Parser $parser needed for testing
     */
    public function __construct(Parser $parser = new Parser())
    {
        $this->parser = $parser;
    }

    /**
     * converts an uploaded bill to an array containing the data
     *
     * @param $fileName string the bill
     * @return array of pdf items
     */
    public function parse(string $fileName): array
    {
        $pages = [];

        try {
            $pdf = $this->parser->parseFile($fileName);
            $pages = $pdf->getPages();
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        // create bill out of pdf
        $bill = new Bill();
        $billDateLoaded = false;

        $lineItems = [];

        // go through each booking type and set $bookingTypeRead to false
        $bookingTypes = new BookingTypes();
        $bookingTypes = $bookingTypes->loadAll();
        $bookingTypesRead = [];
        for ($i = 0; $i < count($bookingTypes); $i++) {
            $bookingTypesRead[] = false;
        }

        foreach ($pages as $page) {
            $text = $page->getText();
            $lines = explode("\n", $text);

            foreach ($lines as $line) {
                $columns = preg_split('/\s+/', trim($line));

                // read in bill date
                if (!$billDateLoaded) $billDateLoaded = $this->readBillDate($bill, $columns);

                // go through all booking types
                foreach ($bookingTypes as $bookingType) {
                    // check if they have been found yet
                    if (!$bookingTypesRead[$bookingType->getId() - 1]) {
                        // this is null if this line does not match to the bookingType
                        $lineItem = $this->readLineItem($columns, $bookingType);
                        if (!is_null($lineItem)) {
                            $bookingTypesRead[$bookingType->getId() - 1] = true;
                            $lineItems[] = $lineItem;
                            break;
                        }
                    }
                }

                // this is needed to quit a pdf file earlier. This saves some time. After "Gesamtkosten Heizanlage" nothing happens anymor in the pdf
                if ($columns[0] == 'Gesamtkosten' && $columns[1] == 'Heizanlage')
                    break 2;
            }
        }

        // gather bill and line items together
        return array(
            "bill" => $bill,
            "real_estate" => new RealEstate(),
            "lineItems" => $lineItems
        );
    }

    /**
     * extract bill date from pdf and add it to the bill object
     *
     * @param $bill Bill where the date is going to be added
     * @param array $columns
     * @return bool if it was loaded
     */
    private function readBillDate(Bill $bill, array $columns): bool
    {
        if (count($columns) >= 2 && $columns[0] == 'Abrechnung') {
            $bill->setYear($columns[1]);

            return true;
        }

        return false;
    }

    /**
     * read out the last money value (e.g. 11,99 and 1.199,99)
     *
     * @param $columns array that is given
     * @return float|string found value
     */
    private function readLastMoneyValue(array $columns): float|string
    {
        if (count($columns) >=3) {
            $columns = array_reverse($columns); // reverse array to read from back to front
            if (preg_match('/^\d+(\.\d{3})*,\d{2}$/', $columns[1])) { // check if $columns[1] is a money value
                return $this->convertToFloat($columns[1]);
            } else {
                $columns[2] .= $columns[1]; // concat $columns[2] and $columns[1] in case the money value is splitted like "28.8 2" instead of "28.82"
                if (preg_match('/^\d+(\.\d{3})*,\d{2}$/', $columns[2])) {
                    return $this->convertToFloat($columns[2]);
                }
            }
        }
        return "0.00";
    }

    /**
     * convert given money value in a usable database format
     *
     * @param $value string of given money
     * @return float of usable database format
     */
    private function convertToFloat(string $value): float
    {
        $normalizedValue = str_replace(['.', ','], ['', '.'], $value);
        return floatval($normalizedValue);
    }

    /**
     * check if a line contains the information of a bookingType, if yes create a new lineItem
     *
     * @param $columns array the split text of the line
     * @param BookingType $bookingType
     * @return LineItem|null
     */
    private function readLineItem(array $columns, BookingType $bookingType): ?LineItem
    {
        $shortName = $bookingType->getShortName();
        if (count($columns) >= 3) {
            $lineBeginning = $columns[0] . ' ' . $columns[1] . ' ' . $columns[2];
            if (str_starts_with($lineBeginning, $shortName)) {
                // set up line item
                $lineItem = new LineItem();
                $lineItem->setDescription($shortName);
                $lineItem->setPrice($this->readLastMoneyValue($columns));
                $lineItem->setBookingType($bookingType);
                return $lineItem;
            }
        }
        return null;
    }
}

?>