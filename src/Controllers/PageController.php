<?php

namespace src\Controllers;

use src\Models\database\implementation\Bill;

require_once __DIR__ . '/../../config/paths.php';

require_once "CorrectionController.php";
require_once "UploadController.php";
require_once "LoginController.php";

/**
 * class forwards to actual controller
 */
class PageController
{
    /**
     * main function that forwards page id to the actual function
     *
     * @param int $page id
     * @return void
     */
    function showPage(int $page): void
    {
        //Session needed to save whether a user is logged in
        session_start();

        //decide what page to go to depending on global page variable in URL
        switch ($page) {
            case 0: $this->home();
                break;
            case 1: $this->correction();
                break;
            case 2: $this->upload();
                break;
            case 3: $this->pagenotfound(); // space for a new site here, deleted downloads
                break;
            case 4: $this->preview();
                break;
            case 5: $this->billOverview();
                break;
            case 6: $this->accountButton();
                break;
            case 7: $this->register();
                break;
            case 8: $this->realEstates();
                break;
            case 9: $this->addRealEstate();
                break;
            case 10: $this->credit();
                break;
            case 11: $this->overviewAnalysisResults();
                break;
            case 12: $this->billAnalysisResults();
                break;
            case 13: $this->expertAnalysisForm();
                break;
            case 14: $this->expertAnalysis();
                break;
            case 15: $this->getBookingTypes();
                break;
            default: $this->pageNotFound();
                break;

        }
    }

    /**
     * Show the homepage
     *
     * @return void
     */
    private function home(): void
    {
        $LoginController = new LoginController();
        if ($LoginController->isLoggedIn()) {
            $welcomeMessage= "Willkommen ".$LoginController->loggedUsername();
        }else{
            $welcomeMessage= "Wilkommen auf der Webseite";
        }
        require BASE_DIRECTORY.'src/Views/header.php';
        $menuController = new MenuController();
        $menuController->displayMenu();
        require BASE_DIRECTORY.'src/Views/home.php';
        require BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * Show the Upload page
     *
     * @return void
     */
    private function upload(): void
    {
        require BASE_DIRECTORY.'src/Views/header.php';
        $menuController = new MenuController();
        $menuController->displayMenu();
        $uploadController = new UploadController();
        $uploadController->main();
        require BASE_DIRECTORY. 'src/Views/footer.php';

    }

    /**
     * Show the Correction page managed by the Correction Controller
     *
     * @return void
     */
    private function correction(): void
    {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();
        $correctionController = new CorrectionController();
        $correctionController->main();
        require BASE_DIRECTORY. 'src/Views/footer.php';
    }

    /**
     * Show a 404 Error page
     *
     * @return void
     */
    private function pagenotfound(): void
    {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();
        require BASE_DIRECTORY.'src/Views/pagenotfound.php';
        require BASE_DIRECTORY. 'src/Views/footer.php';
    }

    /**
     * get a bill overview of the user
     *
     * @return void
     */
    private function billOverview(): void
    {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        require_once BASE_DIRECTORY.'src/Controllers/BillOverview.php';
        $billOverview = new BillOverview();
        $billOverview->showBillOverview();

        require BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * Show a pdf preview
     *
     * @return void
     */
    private function preview(): void
    {
        require BASE_DIRECTORY.'src/Views/previewpage.php';
    }

    /**
     * Displays a login page
     *
     * @return void
     */
    private function accountButton(): void
    {
        $loginController = new LoginController();
        if ($loginController->isLoggedIn()) {
            $loginController->logout();
        }else{
            require BASE_DIRECTORY.'src/Views/header.php';
            $MenuController = new MenuController();
            $MenuController->displayMenu();

            $loginController->showLoginPage();

            require BASE_DIRECTORY.'src/Views/footer.php';
        }

    }

    /**
     * Displays a login page
     *
     * @return void
     */
    private function register(): void
    {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        $loginController = new LoginController();
        $loginController->showRegisterPage();

        require BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * show real estates of the user
     *
     * @return void
     */
    private function realEstates(): void
    {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        require_once BASE_DIRECTORY.'src/Controllers/RealEstateController.php';
        $realEstateController = new RealEstateController();
        $realEstateController->showRealEstates();

        require BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * show page to add a new real estate
     *
     * @return void
     */
    private function addRealEstate(): void {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        require_once BASE_DIRECTORY.'src/Controllers/RealEstateController.php';
        $realEstateController = new RealEstateController();
        $realEstateController->requestAddRealEstates();

        require_once BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * show credit page
     *
     * @return void
     */
    private function credit(): void{

        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        $creditController = new CreditController();
        $creditController->credit();

        require BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * shows all analysing results that were already generated
     *
     * @return void
     */
    private function overviewAnalysisResults(): void {
        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        require BASE_DIRECTORY . 'src/Controllers/AnalysisResultsController.php';
        $analysisResultsController = new AnalysisResultsController();
        $analysisResultsController->showBillOverview();

        require_once BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * show analysis of a specific bill
     *
     * @return void
     */
    private function billAnalysisResults(): void {
        // if no bill_id is given show 'pagenotfound'
        if (!array_key_exists('bill_id', $_GET)) {
            $this->pagenotfound();
            return;
        }

        // create bill item
        require_once DATABASE_DIRECTORY.'implementation/Bill.php';
        $bill_id = $_GET['bill_id'];
        $bill = new Bill($bill_id);

        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        // call controller
        require BASE_DIRECTORY . 'src/Controllers/AnalysisResultsController.php';
        $analysisResultsController = new AnalysisResultsController();
        $analysisResultsController->showBillAnalysis($bill);

        require_once BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * this is an extra site just to compare two different bills in an expert mode
     *
     * @return void
     */
    private function expertAnalysisForm(): void {
        require BASE_DIRECTORY.'src/Views/header.php';
        $menuController = new MenuController();
        $menuController->displayMenu();

        // call controller
        require BASE_DIRECTORY . 'src/Controllers/ExpertAnalysisController.php';
        $expertAnalysisController = new ExpertAnalysisController();
        $expertAnalysisController->showStartForm();

        require_once BASE_DIRECTORY.'src/Views/footer.php';
    }

    /**
     * this shows the expert analysis of two given bills
     *
     * @return void
     */
    private function expertAnalysis(): void {
        // if no bill is given show 'pagenotfound'
        if (!array_key_exists('bill1', $_POST) || !array_key_exists('bill2', $_POST)) {
            $this->pagenotfound();
            return;
        }

        // create bill item
        require_once DATABASE_DIRECTORY.'implementation/Bill.php';
        $bill1_id = $_POST['bill1'];
        $bill1 = new Bill($bill1_id);
        $bill2_id = $_POST['bill2'];
        $bill2 = new Bill($bill2_id);

        require BASE_DIRECTORY.'src/Views/header.php';
        $MenuController = new MenuController();
        $MenuController->displayMenu();

        // call controller
        require BASE_DIRECTORY . 'src/Controllers/ExpertAnalysisController.php';
        $expertAnalysisController = new ExpertAnalysisController();
        $expertAnalysisController->showExpertAnalysis($bill1, $bill2);

        require_once BASE_DIRECTORY.'src/Views/footer.php';
    }

    private function getBookingTypes(): void {
        require BASE_DIRECTORY.'src/Views/booking_types.php';
    }
}