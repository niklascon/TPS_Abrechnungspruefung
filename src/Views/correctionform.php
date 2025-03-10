<script src="<?php use src\Models\database\implementation\Bill;
use src\Models\database\implementation\LineItem;

echo PUBLIC_DIRECTORY . 'js/correctionform.js'?>"></script>

<!-- in the correction form the read in data is displayed to the user with the possibility to change mistakes.
Therefore all entries exist twice, once labeled output, once input (changed between them can be through the showInput() function from add_delete.js).
To deal with possible wrong entries, all entries are submitted through post variables with the data-index-index labels. These post variables are processed in the CorrectionController class.
The beginning code handles bill and real estate information, the second part is for the line items.
add_delete.js is highly relevant for this file, data is exchanged through ids, everytime an id is given, there is a counterpart using it in add_delete or CorrectionController

- input-index-index, output-index-index => showInput (add_delete.js)
- row-index => removeRow (add_delete.js)
- data-index-index => postForm (CorrectionController.php)
-->

<div class="container pt-5">
    <p class="h1">Eingelesene Daten</p>
    <p>Bitte korrigieren und vervollständigen sie die Daten</p>
    <div style="color: red">
        <?php
            //display error message to inform user about what to change
            echo $this->errorMessage;
        ?>
    </div>
    <form method="post" id="correctionForm">
        <table class="table table-striped" id="dataTable">
            <tbody>
            <!-- this part is for the bill and real estate information -->
            <?php
            // this table row is for the bill data
            if (array_key_exists("bill", $this->formData)) {
                if ($this->formData["bill"] instanceof Bill) {
                    $bill = $this->formData["bill"];
                    ?>
                    <!-- tr for bill name and year -->
                    <tr id="row-0" class="editable-row" style="vertical-align: middle; <?php if (count($realEstates) == 1) echo 'border-bottom: 3px solid black; margin-bottom: 10px;'; ?>"  >
                        <td>
                            <label>
                                <input type="text" class="form-control" maxlength="64" name="data-0-0"
                                       value="<?php echo $bill->getName(); ?>"
                                       placeholder="Rechnungsname eingeben"
                                       required>
                            </label>
                        </td>
                        <td>
                            <div style="display: block;">Jahr:</div>
                        </td>
                        <td>
                            <!-- output to show the read in year -->
                            <div style="display: block;" id="output-0-1"><?php echo $bill->getYear(); ?>
                                <a onclick="showInput(0,1)"><img style="height: 1em" src="<?php echo PUBLIC_DIRECTORY?>images/pen.png" alt="edit"/></a>
                            </div>

                            <!-- input if the user changes something -->
                            <div style="display: none" id="input-0-1">
                                <label>
                                    <input type="number" class="form-control" maxlength="4" name="data-0-1" value="<?php echo $bill->getYear(); ?>" step="1" min="1" style="width: 10ch;">
                                </label>
                            </div>
                        </td>
                    </tr>

                    <!-- if user only has 1 real estate, he does not have to choose -->
                    <?php
                    if (count($realEstates) > 1) {
                        ?>
                        <!-- table row to choose real estate -->
                        <tr id="row-1" class="editable-row" style="vertical-align: middle; border-bottom: 3px solid black;">
                            <td style="padding: 10px; min-height: 50px;">
                                <div style="display: block;">Immobilie der Rechnung:</div>
                            </td>
                            <td colspan="2" style="padding: 10px; min-height: 50px;">
                                <div style="display: block; width: 100%;" id="input-1-0">
                                    <label style="width: 100%;">
                                        <select name="data-1-0" id="data-1-0" class="form-control" style="width: 100%;" required>
                                            <option value="" selected>Immobilie wählen</option>
                                            <!-- get all real estates for the drop down menu -->
                                            <?php
                                            # HEAD
                                            $currentRealEstate = $this->formData["real_estate"];
                                            foreach ($realEstates as $realEstate) {
                                                if ($realEstate->getId() == $currentRealEstate->getId()) {
                                                    echo "<option value='" . $realEstate->getId() . "' selected>" . $realEstate->getName() . "</option>";
                                                } else {
                                                    echo "<option value='" . $realEstate->getId() . "'>" . $realEstate->getName() . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                }
            }

            // this table row is for all line items
            if (array_key_exists("lineItems", $this->formData)) {
                $i=2;
                foreach ($this->formData["lineItems"] as $lineItem){
                    if ($lineItem instanceof LineItem) {
                        ?>
                    <tr id="row-<?php echo $i; ?>" class="editable-row" style="vertical-align: middle">
                        <td>
                            <!-- output to show the read in line item -->
                            <div style="display: block;" id="output-<?php echo $i.'-0'?>">
                                <?php echo $lineItem->getDescription(); ?>
                                <a onclick="showInput(<?php echo $i.',0'?>)">
                                    <img style="height: 1em" src="<?php echo PUBLIC_DIRECTORY?>images/pen.png" alt="edit"/>
                                </a>
                            </div>
                            <!-- input if the user changes something -->
                            <div style="display: none" id="input-<?php echo $i.'-0'?>">
                                <label>
                                    <!-- drop down menu with all the bookingtypes -->
                                    <select name="data-<?php echo $i . '-0'; ?>" id="data-<?php echo $i . '-0'; ?>">
                                        <?php
                                        $currentDescription = $lineItem->getDescription();

                                        // get all booking types
                                        $bookingTypesModel = new \src\Models\database\implementation\BookingTypes();
                                        $bookingTypes = $bookingTypesModel->loadAll();

                                        // set the default value on the top
                                        echo '<option value="' . htmlspecialchars($currentDescription) . '" selected>' . htmlspecialchars($currentDescription) . '</option>';

                                        // show all booking types as options
                                        foreach ($bookingTypes as $bookingType) {
                                            $shortName = htmlspecialchars($bookingType->getShortName());

                                            // skip the booking type that we selected before
                                            if ($shortName === $currentDescription) {
                                                continue;
                                            }

                                            echo "<option value='$shortName'>$shortName</option>";
                                        }
                                        ?>
                                    </select>
                                </label>
                            </div>
                        </td>
                        <td>
                            <!-- output to show the read in line item price -->
                            <div style="display: block;" id="output-<?php echo $i.'-1'?>">
                                <?php echo $lineItem->getPrice(); ?>
                                <a onclick="showInput(<?php echo $i.',1'?>)">
                                    <img style="height: 1em" src="<?php echo PUBLIC_DIRECTORY?>images/pen.png" alt="edit"/>
                                </a>
                            </div>
                            <!-- input if the user changes something -->
                            <div style="display: none" id="input-<?php echo $i.'-1'?>">
                                <label>
                                    <input type="number" min="0" step="0.01" class="form-control" maxlength="64" name="data-<?php echo $i.'-1'?>" value="<?php echo $lineItem->getPrice(); ?>">
                                </label>
                            </div>
                        </td>
                        <td>
                            <!-- delete button -->
                            <button type="button" class="btn btn-outline-danger" onclick="removeRow(<?php echo $i; ?>)">
                                <i class="bi bi-trash"></i> Löschen
                            </button>
                        </td>
                        </tr><?php

                        $i++;
                    }
                }
            }

            ?>
            </tbody>
        </table>
        <!-- Add Row Button -->
        <button type="button" class="btn btn-outline-success" id="addRowBtn" onclick="addRow()">Reihe hinzufügen</button>

        <button type="submit" class="btn btn-primary">Absenden</button>
    </form>
</div>

<script src="<?php echo PUBLIC_DIRECTORY . 'js/add_delete.js' ?>"></script>

