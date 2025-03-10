<script src="<?php echo PUBLIC_DIRECTORY . 'js/correctionform.js'?>"></script>

<p class="h1 mb-4 text-center">Expertenanalyse</p>
<p class="text-center text-muted">Wählen Sie zwei Rechnungen aus, die Sie vergleichen möchten:</p>

<!-- Form to choose Bills -->
<form action="<?php echo PUBLIC_DIRECTORY . 'index.php?page=14'; ?>" method="post">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="bill1" class="form-label">Zu untersuchende Rechnung</label>
                    <select id="bill1" name="bill1" class="form-select" required data-bs-toggle="tooltip" title="Wählen Sie die Rechnung, die Sie untersuchen möchten.">
                        <option value="" disabled <?php echo (!isset($bill1)) ? 'selected' : ''; ?>>
                            Wählen Sie eine Rechnung aus
                        </option>
                        <?php foreach ($billList as $bill): ?>
                            <option value="<?php echo $bill->getId(); ?>"
                                <?php echo (isset($bill1) && $bill->getId() == $bill1->getId()) ? 'selected' : ''; ?>>
                                <?php echo $bill->getName(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="bill2" class="form-label">Referenzrechnung</label>
                    <select id="bill2" name="bill2" class="form-select" required data-bs-toggle="tooltip" title="Wählen Sie eine Referenzrechnung für den Vergleich aus.">
                        <option value="" disabled <?php echo (!isset($bill2)) ? 'selected' : ''; ?>>
                            Wählen Sie eine Rechnung aus
                        </option>
                        <?php foreach ($billList as $bill): ?>
                            <option value="<?php echo $bill->getId(); ?>"
                                <?php echo (isset($bill2) && $bill->getId() == $bill2->getId()) ? 'selected' : ''; ?>>
                                <?php echo $bill->getName(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <p class="text-center text-muted">Hier können Sie die Abweichung für einzelne Buchungstypen anpassen (standardmäßig sind 10% eingestellt):</p>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <table class="table table-hover table-striped" id="dataTable">
                <thead>
                <tr>
                    <th>Buchungstyp</th>
                    <th>Abweichung (%)</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach (array_keys($this->deviationThresholds) as $key) {
                        ?>
                        <tr id="row-<?php echo $i; ?>" class="editable-row" style="vertical-align: middle">
                            <td>
                                <div style="display: block" id="output-<?php echo $i.'-0'?>">
                                    <?php echo $key;
                                    if ($key != 'Standard') {
                                        ?>
                                    <a onclick="showInput(<?php echo $i.',0'?>)">
                                        <img style="height: 1em" src="<?php echo PUBLIC_DIRECTORY?>images/pen.png" alt="edit"/>
                                    </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div style="display: none" id="input-<?php echo $i.'-0'?>">
                                    <label>
                                        <select name="data-<?php echo $i . '-0'; ?>" id="data-<?php echo $i . '-0'; ?>">
                                            <?php
                                            $currentDescription = $key;
                                            // load BookingTypes
                                            $bookingTypesModel = new \src\Models\database\implementation\BookingTypes();
                                            $bookingTypes = $bookingTypesModel->loadAll();

                                        echo '<option value="' . htmlspecialchars($currentDescription) . '" selected>' . htmlspecialchars($currentDescription) . '</option>';

                                        // load all possible options
                                        foreach ($bookingTypes as $bookingType) {
                                            $shortName = htmlspecialchars($bookingType->getShortName());

                                                // skip first option
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
                                <div style="display: block;" id="output-<?php echo $i.'-1'?>">
                                    <?php echo $this->deviationThresholds[$key]; ?>
                                    <a onclick="showInput(<?php echo $i.',1'?>)">
                                        <img style="height: 1em" src="<?php echo PUBLIC_DIRECTORY?>images/pen.png" alt="edit"/>
                                    </a>
                                </div>

                                <div style="display: none" id="input-<?php echo $i.'-1'?>">
                                    <label>
                                        <input type="number" min="0" step="0.01" class="form-control" maxlength="64" name="data-<?php echo $i.'-1'?>" value="<?php echo $this->deviationThresholds[$key]; ?>">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <?php
                                if ($key != 'Standard') { ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(<?php echo $i; ?>)">
                                        <i class="bi bi-trash"></i> Löschen
                                    </button>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                            <?php

                            $i++;
                }
            ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-success btn-sm" id="addRowBtn" onclick="addRow()">Reihe hinzufügen</button>
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg">Vergleichen</button>
    </div>
</form>

<?php if (empty($billList)): ?>
    <div class="alert alert-warning mt-3" role="alert">
        Es sind keine Rechnungen verfügbar, die Sie vergleichen können.
    </div>
<?php endif; ?>

<hr>

<script src="<?php echo PUBLIC_DIRECTORY . 'js/add_delete.js' ?>"></script>


<script src="<?php echo PUBLIC_DIRECTORY . 'js/expert.js' ?>"></script>

<?php
echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/expertAnalysis.css'>";
?>
