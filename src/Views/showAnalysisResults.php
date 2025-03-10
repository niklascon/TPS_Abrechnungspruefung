<div class="container pt-5">
    <p class="h1">Prüfungsergebnis: <?php use src\Models\database\implementation\ItemsOfAnalysis;

        echo $bill->getName(); ?></p>
    <table class="table table-striped">
        <?php
        foreach ($allAnalysisResults as $analysisResult) { ?>
            <tr>
                <td>
                    <div class="bubble">
                        <p class="bubble-text"><?php echo $analysisResult->getPriceDevelopment(); ?></p>
                        <p class="bubble-text"><?php echo $analysisResult->getTextBracket()->getTextBracket(); ?></p>
                        <?php
                        require_once DATABASE_DIRECTORY.'implementation/ItemsOfAnalysis.php';
                        $itemsOfAnalysis = new ItemsOfAnalysis();
                        $itemsOfAnalysis->addWhereAnalysis($analysisResult);
                        $allItemsOfAnalysis = $itemsOfAnalysis->loadAll();
                        if ($allItemsOfAnalysis) {
                            ?>
                            <br><p class="bubble-text"><?php echo $allItemsOfAnalysis[0]->getLineItem()->getBookingType()->getShortName(); ?>: </p>
                            <?php
                        }
                        foreach ($allItemsOfAnalysis as $item) {
                            ?>
                            <p class="bubble-text">Wert aus Rechnung <b><?php echo $item->getLineItem()->getBill()->getName(); ?></b>
                                (Jahr: <?php echo $item->getLineItem()->getBill()->getYear(); ?>):
                                <?php echo $item->getLineItem()->getPrice(); ?>€</p><br>
                            <?php
                        }

                        ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
