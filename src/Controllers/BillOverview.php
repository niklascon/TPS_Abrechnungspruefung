<?php
namespace src\Controllers;

use src\Models\database\implementation\Bills;

/**
 * this class gives an overview over all bills of the user
 */
class BillOverview {
    /**
     * view of bill overview
     *
     * @return void
     */
    public function showBillOverview(): void
    {
        require_once DATABASE_DIRECTORY.'implementation/Bills.php';
        // we need the login controller to check if user is logged in
        $loginController = new LoginController();
        $bills = new Bills();
        $billList = [];
        if ($loginController->isLoggedIn()) {
            $userName = $loginController->loggedUsername();
            // only those connected to the user get added
            $bills->addJoinUser($userName);
            $billList = $bills->loadAll();

            // show bills if user is logged in
            $billList = $this->postForm($billList);
            require BASE_DIRECTORY.'src/Views/showBillOverview.php';
        } else {
            require BASE_DIRECTORY.'src/Views/showEmptyBillOverview.php';
        }
    }

    /**
     * manages changes in the bill overview (deletion by users)
     *
     * @param $billList array list of bills that existed when entering the site
     * @return array
     */
    private function postForm(array $billList): array {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            for ($i = count($billList) - 1; $i >= 0; $i--) {
                if (!array_key_exists("row-" . $i, $_POST)) { // row was deleted
                    $billList[$i]->delete();
                    unset($billList[$i]);
                }
            }
        }
        return $billList;
    }
}
