<?php
    echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/analysisResultsStyle.css'>";
?>

<div class="container pt-5">
    <p class="h1">Prüfungsergebnisse</p>
    <table class="table table-striped">
        <?php
        if (!$billList) {?>
            Sie haben noch keine Rechnungen hochgeladen.
            <?php
        }

        $hasAnalysisResults = false;

        // iterate throw all bills
        foreach ($billList as $bill){?>
            <tr>
            <td class="icon-cell">
                <?php
                // show i only if bill has analysis results
                if ($bill->hasAnalysisResult()) {
                    $hasAnalysisResults = true;
                    ?>
                    <a class="icon-link" href="index.php?page=12&bill_id=<?php echo $bill->getId(); ?>">
                        ⓘ
                        <span class="tooltip-text">Diese Rechnung könnte Irrtümer enthalten!</span>
                    </a>
                    <?php
                }
                ?>
            </td>
            <td>
                <div style="overflow: hidden;">
                    <span style="float: left;"><?php echo $bill->getName(); ?></span>
                </div>
            </td>
            </tr><?php
        }
        ?>
    </table>
    <?php
        if ($billList && !$hasAnalysisResults) {?>
            <span style="color: green;">Keine deiner Rechnung entählt Irrtümer!</span><br><br>
            <?php
        } ?>
    <a class="btn btn-primary" href="index.php?page=2">Rechnung hinzufügen</a>
</div>