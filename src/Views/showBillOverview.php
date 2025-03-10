<?php
echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/billOverviewStyle.css'>";
?>
<div class="container pt-5">
    <div class="d-flex justify-content-between align-items-center">
        <p class="h1 mb-0">Rechnungen</p>
        <a class="btn btn-primary" href="index.php?page=2">Rechnung hinzufügen</a>
    </div>
    <hr class="custom-divider">
    <script src="<?php echo PUBLIC_DIRECTORY . 'js/add_delete.js' ?>"></script>
    <form method="POST" onsubmit="confirmDeletion(event)">
    <table class="table table-striped" id="dataTable">
        <tbody>
        <?php
        if (!$billList) {?>
            Sie haben noch keine Rechnungen hochgeladen.
            <?php
        }
        $i = 0;
        // iterate throw all bills
        foreach ($billList as $bill){?>
            <tr id="row-<?php echo $i; ?>" class="editable-row" style="vertical-align: middle">
                <td>
                    <div style="overflow: hidden;">
                        <span style="float: left;" ><?php echo $bill->getName(); ?></span>
                        <a style="float: right;"><img style="height: 1em" src="<?php echo PUBLIC_DIRECTORY?>images/pen.png" alt="edit" /></a>
                    </div>
                    <div style="display: none" id="input-0-0">
                        <label>
                            <input type="text" class="form-control" maxlength="64" name="row-<?php echo $i; ?>" value="<?php echo $bill->getName(); ?>">
                        </label>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger" onclick="addSubmitBillDelete(); markRowForDeletion(this)">
                        <i class="bi bi-trash"></i> Löschen
                    </button>
                </td>
            </tr><?php
            $i++;
        } ?>
        </tbody>
    </table>
        <button id="submit" style="display: none" type="submit" class="btn btn-primary">Ausgewählte löschen</button>
        <a  id="reload" style="display: none"  class="btn btn-secondary" href="index.php?page=5">Abbrechen</a>
    </form>
</div>