<?php
namespace src\Controllers;

use src\Models\database\implementation\AnalysisResults;
use src\Models\database\implementation\Bill;
use src\Models\database\implementation\Bills;
use src\Models\database\implementation\Users;

require_once DATABASE_DIRECTORY.'implementation/Bill.php';

/**
 * this class gives an overview over all analysing result of the scanned PDFs
 */
class AnalysisResultsController
{
    /**
     * this shows a bill overview containing information of analysis results
     *
     * @return void
     */
    public function showBillOverview(): void {
        require_once DATABASE_DIRECTORY.'implementation/Bills.php';
        // we need the login controller to check if user is logged in
        $loginController = new LoginController();
        if ($loginController->isLoggedIn()) {
            $userName = $loginController->loggedUsername();
            $bills = new Bills();
            // only those connected to the user get added
            $bills->addJoinUser($userName);
            $billList = [];
            $billList = $bills->loadAll();

            // show bills if user is logged in
            require BASE_DIRECTORY.'src/Views/showAnalysisResultsOverview.php';
        } else {
            require BASE_DIRECTORY.'src/Views/showEmptyAnalysisResults.php';
        }
    }

    /**
     * show analysis results of a specific bill that the user owns
     * first check if the user has the permissions to see the bill
     *
     * @param Bill $bill
     * @return void
     */
    public function showBillAnalysis(Bill $bill): void {
        // we need the login controller to check if user is logged in
        $loginController = new LoginController();
        if (!$loginController->isLoggedIn()) {
            require BASE_DIRECTORY.'src/Views/showEmptyAnalysisResults.php';
            return;
        }

        // check if the user has the right to see the bill
        $userName = $loginController->loggedUsername();
        $users = new Users();
        $users->addWhereUserName($userName);
        $allUsers = $users->loadAll();
        // check if a user exists;
        if (!$allUsers) {
            require BASE_DIRECTORY.'src/Views/noPermission.php';
            return;
        }
        // check if user has permission to access site
        if (!$bill->hasUserPermission($allUsers[0])) {
            require BASE_DIRECTORY.'src/Views/noPermission.php';
            return;
        }

        // this actually shows the bill analysis
        $this->accessBillAnalysis($bill);
    }

    /**
     * show analysis of specific bill
     *
     * @param Bill $bill of analysis results
     * @return void
     */
    private function accessBillAnalysis(Bill $bill): void {
        // get all analysis results of given bill
        require_once DATABASE_DIRECTORY.'implementation/AnalysisResults.php';
        $analysisResults = new AnalysisResults();
        $analysisResults->addJoinBill($bill);
        $allAnalysisResults = $analysisResults->loadAll();

        require BASE_DIRECTORY.'src/Views/showAnalysisResults.php';
    }
}