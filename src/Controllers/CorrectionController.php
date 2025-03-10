<?php

namespace src\Controllers;

use PhpParser\Node\Scalar\String_;
use src\Models\analysis_result_generator\AnalysisResultGenerator;
use src\Models\database\core\Form;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\Bills;
use src\Models\database\implementation\BookingTypes;
use src\Models\database\implementation\LineItem;
use src\Models\database\implementation\RealEstate;
use src\Models\pdf_parsing\PDFParser;
use src\Models\database\implementation\Users;

require_once __DIR__ . '/../../config/paths.php';
require_once BASE_DIRECTORY.'src/Models/pdf_parsing/PDFParser.php';
require_once DATABASE_DIRECTORY.'implementation/Bill.php';
require_once DATABASE_DIRECTORY.'implementation/LineItem.php';
require_once DATABASE_DIRECTORY.'implementation/LineItems.php';
require_once DATABASE_DIRECTORY.'implementation/BookingTypes.php';


/**
 * When a user uploads a file he is then asked to verify if Data has been read correctly
 */
class CorrectionController
{
    private $formData;
    private $loggedUser;
    private String $errorMessage = "";

    public function __construct($user = null) {
        if ($user == null) {
            // TODO this is the current workaround to get the user
            $loginController = new LoginController();
            $userName = $loginController->loggedUsername();
            $users = new Users();
            $users->addWhereUserName($userName);
            $this->loggedUser = $users->loadAll()[0];
        } else {
            // needed for testing
            $this->loggedUser = $user;
        }
    }

    /**
     * Function Generating a Form of Data to be corrected by the user
     *
     * @return void
     */
    function main(): void
    {

        if ($this->formData == null) $this->formData = $this->getData(); // get bill and line item data
        $file = $this->getFileFrame();
        $this->errorMessage = ""; // Displayed to user to explain, what he has to complete
        // get the real estates corresponding to the user
        $realEstates = $this->loggedUser->getRealEstatesOfUser();

        require_once BASE_DIRECTORY . 'src/Views/correctionpreview.php';
    }

    /**
     * Call PDF Parser and get line items
     *
     * @return array[] with index bill and lineItems
     */
    private function getData(): array
    {
        $parser = new PDFParser();
        $fileName = PUBLIC_DIRECTORY . '/uploads/' . $_GET['file']; // TODO get the filename in a more beautiful way
        $data = $parser->parse($fileName);
        return $data;
    }

    /**
     * changes the attributes of the bill object to the values given through the post variables
     *
     * @param Form $form instance of the Form class to check for valid inputs
     * @param bool $mistake whether there is already a mistake made
     * @return bool $whether there were mistakes in the bill inputs
     */
    private function changeBillAttributes(Form $form, bool $mistake): bool
    {
        $reading = $_POST["data-" . "0-" . "0"];
        $this->formData["bill"]->setName($reading);
        //If read name is empty just mark it red
        if (!$form->isBillNameValid($reading)) {
            $mistake = true;
            if ($reading != "") { //The user should be informed to not use special characters
                $this->errorMessage =   $this->errorMessage . "<p>Der Rechnungsname darf nur 
                                        Buchstaben und Ziffern sowie Leerzeichen, Bindestriche 
                                        und Unterstriche enthalten</p>";
            }
        }
        $reading = $_POST["data-" . "0-" . "1"];
        $this->formData["bill"]->setYear((int)$reading);
        if (!$form->isYearValid($reading)) {
            $mistake = true;
            if ($reading != "") { //The user should be informed to not use a valid year format
                $this->errorMessage =   $this->errorMessage . "<p>Das Jahr muss aus 
                                        vier Ziffern bestehen</p>";
            }
        }
        return $mistake;
    }

    /**
     * Read the Realestate chosen by the user, verify that it has been set to an existing real
     * estate
     * @param Form $form instance of the Form class to check for valid inputs
     * @param bool $mistake whether there is already a mistake made
     * @return bool $whether there were mistakes in the bill inputs
     */
    private function changeRealEstateAttributes(Form $form, bool $mistake): bool
    {
        if (array_key_exists("data-1-0", $_POST)) {
            $chosenRealEstate = $_POST["data-1-0"];
            $this->formData["real_estate"]->setId((int)$chosenRealEstate);
            if (!$form->isRealEstateValid($chosenRealEstate, $this->loggedUser)) {
                $mistake = true;
            }
        } else {
            // choose the one real estate of the user that exists
            $realEstates = $this->loggedUser->getRealEstatesOfUser();
            $this->formData["real_estate"]->setId((int)$realEstates[0]->getId());
        }

        return $mistake;
    }

    /**
     * changes the attributes of the line item object to the values given through the post variables
     *
     * @param Form $form instance of the Form class to check for valid inputs
     * @param bool $mistake whether there is already a mistake made
     * @return bool $whether there were mistakes in the line item inputs
     */
    private function changeLineItemAttributes(Form $form, bool $mistake): bool
    {
        for ($j = 0; $j < count($this->formData["lineItems"]); $j++) {
            if (isset($this->formData["lineItems"][$j]) && $this->formData["lineItems"][$j] instanceof LineItem) {
                $lineItem = $this->formData["lineItems"][$j];
                if (array_key_exists("data-" . ($j + 2) . "-" . "0", $_POST)) {
                    $reading = $_POST["data-" . ($j + 2) . "-" . "0"];
                    $mistake = $this->changeLineItemDescription($form, $mistake, $reading, $lineItem);
                    $reading = $_POST["data-" . ($j + 2) . "-" . "1"];
                    $mistake = $this->changeLineItemPrice($form, $mistake, $reading, $lineItem);
                } else {
                    $this->formData["lineItems"][$j] = null;
                }
            }
        }

        $mistake = $this->addLineItems($form, $mistake);
        return $mistake;
    }

    /**
     * changes the description of the line item and its booking type attribute
     *
     * @param Form $form instance of the Form class to check for valid inputs
     * @param bool $mistake whether there is already a mistake made
     * @param string $reading the read data
     * @param LineItem $lineItem the line item to be changed
     * @return bool $whether there were mistakes in the line item inputs
     */
    private function changeLineItemDescription(Form $form, bool $mistake, string $reading, LineItem $lineItem): bool
    {
        $lineItem->setDescription($reading);

        if ($form->isLineItemValid($reading)) {
            $bookingTypes = new BookingTypes();
            $bookingTypes->addWhereShortName($lineItem);
            $bookingTypesList = $bookingTypes->loadAll();
            $lineItem->setBookingType($bookingTypesList[0]);
        } else {
            $lineItem->setDescription("-- Wählen Sie --");
            $mistake = true;
        }
        return $mistake;
    }


    /**
     * changes the price of the line item
     *
     * @param Form $form instance of the Form class to check for valid inputs
     * @param bool $mistake whether there is already a mistake made
     * @param string $reading the read data
     * @param LineItem $lineItem the line item to be changed
     * @return bool $whether there were mistakes in the line item inputs
     */
    private function changeLineItemPrice(Form $form, bool $mistake, string $reading, LineItem $lineItem): bool{
        $lineItem->setPrice($reading);
        if (!$form->isPriceValid($reading)) {
            if ($reading=="" || $reading=="Preis"){
                $lineItem->setPrice("Preis");
            }else{
                $this->errorMessage=    $this->errorMessage . "<p>Ein Preis besteht entweder aus einer ganzen Zahl oder 
                                        aus einer Zahl mit einer oder zwei Nachkommastellen</p>";
            }

            $mistake = true;
        }
        return $mistake;
    }

    /**
     * adds new line items if users added a row
     *
     * @param Form $form instance of the Form class to check for valid inputs
     * @param bool $mistake whether there is already a mistake made
     * @return bool $whether there were mistakes in the line item inputs
     */
    private function addLineItems(Form $form, bool $mistake): bool {
        $idx = count($this->formData["lineItems"]) + 2;
        while (array_key_exists("data-" . $idx . "-" . "0", $_POST)) {
            $reading = $_POST["data-" . $idx . "-" . "0"];
            $lineItem = new LineItem();
            $this->formData["lineItems"][$idx] = $lineItem;
            $mistake = $this->changeLineItemDescription($form, $mistake, $reading, $lineItem, $idx);

            $reading = $_POST["data-" . $idx . "-" . "1"];
            $mistake = $this->changeLineItemPrice($form, $mistake, $reading, $lineItem, $idx);

            $idx = $idx + 1;
        }
        return $mistake;
    }

    /**
     * Prevents a user from uploading a bill to a realestate and a year with an existing bill
     * @param bool $mistake
     * @param Bills $bills optional, used for tests if a mock class is inserted
     * @return bool true if mistake is true or the bill exists
     */
    private function billNotYetExisting(bool $mistake, Bills $bills=null): bool
    {
        if ($bills==null){ //for testing purposes bills can be passed as parameter
            $bills = new Bills();
        }
        if (array_key_exists("bill", $this->formData)&&array_key_exists("real_estate", $this->formData)) {
            $bills->addWhereRealEstate($this->formData["real_estate"]);
            $bills->addWhereYear($this->formData["bill"]->getYear());
            $sameBills = $bills->loadAll();
            if (!empty($sameBills)){
                $this->errorMessage =   $this->errorMessage . "<p>Es existiert bereits eine Rechnung aus dem Jahr "
                                        .$this->formData["bill"]->getYear(). " für die Immobilie '"
                                        .$this->formData["real_estate"]->getName() . "'.  Wenn Sie die alte Rechnung
                                        ersetzen wollen, löschen Sie zuerst die Rechnung '". $sameBills[0]->getName()
                                        ."' aus der <a href='index.php?page=5'>Rechnungsübersicht</a>.</p>";
                return true;
            }
            return $mistake;
        }
        return true;
    }

    /**
     * handles the changed inputs from the correction form
     * gets called inside correctionpreview.php
     *
     * @return int 0, 1 or 2: 0 means all correct inputs, 1 means invalid inputs, 2 is only when a document is first uploaded
     */
    private function postForm(): bool
    {
        $form = new Form();
        $bills = new Bills();
        $mistake = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mistake = $this->changeBillAttributes($form, $mistake);
            $mistake = $this->changeRealEstateAttributes($form, $mistake);
            $mistake = $this->changeLineItemAttributes($form, $mistake);
            $mistake = $this->billNotYetExisting($mistake);


            if (!$mistake) {
                $bill = $this->saveData($this->formData);

                if (!is_null($bill)) {
                    require_once BASE_DIRECTORY.'src/Models/analysis_result_generator/AnalysisResultGenerator.php';
                    $analysisResultGenerator = new AnalysisResultGenerator();
                    $analysisResultGenerator->preYearComparison($bill);
                }
                return true;
            }
        }
        return false; // there were incorrect entries
    }

    /**
     * the updated values get stored in the database
     *
     * @param array $data the data from the correction page
     * @return Bill of saved bill
     */
    private function saveData(array $data): Bill
    {
        $bill = null;
        if (array_key_exists("bill", $data) && array_key_exists("lineItems", $data)) {
            if ($data["bill"] instanceof Bill) {
                $bill = $data["bill"];
                $bill->setRealEstate($data["real_estate"]);
                $bill->save();

                foreach ($data["lineItems"] as $lineItem) {
                    if ($lineItem instanceof LineItem) {
                        $lineItem->setBill($bill); // bill needs to be saved first (because the id of it is needed)
                        $lineItem->save();
                    }
                }
            }
        }

        return $bill;
    }

    /**
     * Retrieve the file and load it into the Google preview frame.
     *
     * @return string HTML string to embed the file preview or a message if no file is selected.
     */
    private function getFileFrame(): string
    {
        if (!empty($_GET['file'])) {
            $encodedFile = $_GET['file'];
            return "<iframe src='" . PUBLIC_DIRECTORY . "index.php?page=4&file=$encodedFile' width='100%' height='600px'></iframe>";
        }

        // Return a message if no file is selected
        return "No file selected for display.";
    }
}
