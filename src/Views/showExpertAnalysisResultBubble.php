<?php
echo "<link rel='stylesheet' href='".PUBLIC_DIRECTORY."css/expertAnalysis.css'>";
?>
<div class="bubble">
    <br>

    <p class="bubble-text">
        <strong class="booking-type"><?php echo $itemOfAnalysis1->getLineItem()->getBookingType()->getShortName(); ?>:</strong>
    </p>

    <div class="invoice-values">
        <p class="bubble-text">
            Wert aus Rechnung <strong><?php echo $bill1->getName(); ?></strong>
            (Jahr: <?php echo $bill1->getYear(); ?>):
            <span class="price"><?php echo $itemOfAnalysis1->getLineItem()->getPrice(); ?>€</span>
        </p>

        <p class="bubble-text">
            Wert aus Rechnung <strong><?php echo $bill2->getName(); ?></strong>
            (Jahr: <?php echo $bill2->getYear(); ?>):
            <span class="price"><?php echo $itemOfAnalysis2->getLineItem()->getPrice(); ?>€</span>
        </p>
    </div>

    <br>
</div>

<!-- Stylish horizontal line -->
<hr class="styled-hr">
