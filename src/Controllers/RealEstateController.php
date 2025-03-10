<?php
namespace src\Controllers;

use src\Models\database\implementation\Bills;
use src\Models\database\implementation\RealEstate;
use src\Models\database\implementation\RealEstates;
use src\Models\database\implementation\UserRealestate;
use src\Models\database\implementation\Users;

require_once BASE_DIRECTORY.'src/Controllers/LoginController.php';

/**
 * this class gives an overview over all bills of the user
 */
class RealEstateController {
    private LoginController $loginController;

    private string $errorMessage = "";

    /**
     * @param LoginController $loginController is needed for testing
     */
    public function __construct(LoginController $loginController = new LoginController())
    {
        $this->loginController = $loginController;
    }

    /**
     * view of Real Estates
     *
     * @return void
     */
    public function showRealEstates(): void
    {
        require_once DATABASE_DIRECTORY.'implementation/RealEstates.php';
        // we need the login controller to check if user is logged in
        $realEstates = new RealEstates();
        $realEstatesList = [];
        if ($this->loginController->isLoggedIn()) {
            $userName = $this->loginController->loggedUsername();
            // only those connected to the user get added
            $realEstates->addJoinUser($userName);
            $realEstatesList = $realEstates->loadAll();

            // show real estates if user is logged in
            $realEstatesList = $this->postForm($realEstatesList);
            require BASE_DIRECTORY.'src/Views/showRealEstates.php';
        } else {
            require BASE_DIRECTORY.'src/Views/showEmptyRealEstates.php';
        }

    }

    /**
     * manages changes in the real estate overview (deletion by users)
     *
     * @param $realEstatesList array list of real estates that existed when entering the site
     * @return array
     */
    private function postForm(array $realEstatesList): array {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // show error message if user deletes all real estates
            if (!$_POST) {
                $this->errorMessage = "Achtung: Sie können nicht alle Immobilien löschen!";
                return $realEstatesList;
            }

            for ($i = count($realEstatesList) - 1; $i >= 0; $i--) {
                if (!array_key_exists("row-" . $i, $_POST)) { // row was deleted
                    $this->deleteRealestateAndBills($realEstatesList[$i]);

                    unset($realEstatesList[$i]);
                }
            }
        }
        return $realEstatesList;
    }

    /**
     * delete given real estate and all corresponding bills, line items and analysis results
     *
     * @param RealEstate $realEstate
     * @return void
     */
    private function deleteRealestateAndBills(RealEstate $realEstate): void
    {
        // delete all corresponding bills, line items and analysis results
        $bills = new Bills();
        $bills->addWhereRealEstate($realEstate);
        foreach ($bills->loadAll() as $bill) {
            $bill->delete();
        }
        // finally delete the real estate and remove it from the array
        $realEstate->delete();
    }

    /**
     * adding a new real estate but first check if user is logged in
     *
     * @return void
     */
    public function requestAddRealEstates(): void {
        // check if user is logged in
        if ($this->loginController->isLoggedIn()) {
            require_once BASE_DIRECTORY.'src/Views/addRealEstate.php';

            // when "Hinzufügen" is pressed
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $realEstate = $_POST["realEstateName"];

                $this->addRealEstate($realEstate);
            }
        } else {
            // user is not logged in, so empty page shown
            require BASE_DIRECTORY.'src/Views/showEmptyRealEstates.php';
        }
    }

    /**
     * add real estate
     *
     * @param string $realEstateName name of real estate
     * @return void
     */
    private function addRealEstate(string $realEstateName): void
    {
        if (!$this->realEstateExists($realEstateName)) {
            $this->createNewRealEstate($realEstateName);
            // TODO this could be made prettier
            echo "Real Estate hinzugefügt!";
        } else {
            echo "Es existiert bereits ein Real Estate mit diesem Namen.";
        }
    }

    /**
     * checks if the user that is logged-in user already has a real estate with the same name
     *
     * @param string $realEstateName
     * @return bool
     */
    private function realEstateExists(string $realEstateName): bool {
        $userName = $this->loginController->loggedUsername();
        $realEstates = new RealEstates();
        $realEstates->addJoinUser($userName);
        return count($realEstates->loadAllWithName($realEstateName))>0;
    }

    /**
     * adds a new real estate to the database
     *
     * @param string $realEstateName
     * @return void
     */
    private function createNewRealEstate(string $realEstateName): void {
        $realEstate = new RealEstate();
        $realEstate->setName($realEstateName);
        $realEstate->save();

        $userName = $this->loginController->loggedUsername();
        $users = new Users();
        $users->addWhereUserName($userName);
        $user = $users->loadAll();

        $userRealEstate = new UserRealEstate();
        $userRealEstate->setUser($user[0]);
        $userRealEstate->setRealEstate($realEstate);
        $userRealEstate->save();
    }
}
