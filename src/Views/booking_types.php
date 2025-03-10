<?php
// booking_types.php

use src\Models\database\implementation\BookingTypes;

require_once BASE_DIRECTORY . 'src/Models/database/implementation/BookingTypes.php';

$bookingTypesModel = new BookingTypes();
$bookingTypes = $bookingTypesModel->loadAll();

$options = [];
foreach ($bookingTypes as $bookingType) {
    $options[] = [
        'value' => $bookingType->getShortName(),
        'text' => $bookingType->getShortName() . ' ' . $bookingType->getDescription()
    ];
}

header('Content-Type: application/json');
echo json_encode($options);