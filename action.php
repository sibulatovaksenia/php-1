<?php

function filteredAbonents($abonents, $owner, $minCall)
{
    $filteredAbonents = [];
    foreach ($abonents as $abonent) {
        if (strtolower($abonent['owner']) === strtolower($owner) && $abonent['call_duration'] >= $minCall) {
            $filteredAbonents[] = $abonent;
        }
    }
    return $filteredAbonents;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['owner']) && isset($_GET['minCall'])) {
    $owner = $_GET['owner'];
    $minCall = (int)$_GET['minCall'];
    $filteredAbonents = filteredAbonents($abonents, $owner, $minCall);
} else {
    $filteredAbonents = $abonents;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        array_key_exists('phone_number', $_POST) &&
        array_key_exists('address', $_POST) &&
        array_key_exists('owner', $_POST) &&
        array_key_exists('call_duration', $_POST) &&
        array_key_exists('bill', $_POST)
    ) {
        $phone_number = intval($_POST['phone_number']);
        $address = $_POST['address'];
        $owner = $_POST['owner'];
        $call_duration = (int)$_POST['call_duration'];
        $bill = (int)$_POST['bill'];


        $abonentExists = false;

        foreach ($abonents as $key => $abonent) {
            if ($abonent['phone_number'] == $phone_number) {

                $abonents[$key] = [
                    'phone_number' => $phone_number,
                    'address' => $address,
                    'owner' => $owner,
                    'call_duration' => $call_duration,
                    'bill' => $bill,
                ];
                $abonentExists = true;
                break;
            }
        }

        if (!$abonentExists) {
            $abonents[] = [
                'phone_number' => $phone_number,
                'address' => $address,
                'owner' => $owner,
                'call_duration' => $call_duration,
                'bill' => $bill,
            ];
        }
    }
}
