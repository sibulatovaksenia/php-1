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

function saveAbonentsToFile($filename, $abonents)
{
    $json_data = json_encode($abonents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($filename, $json_data);
}

function loadAbonentsFromFile($filename)
{
    if (file_exists($filename)) {
        $json_data = file_get_contents($filename);
        return json_decode($json_data, true);
    }
    return [];
}

$filename = 'abonents.json';
$abonents = loadAbonentsFromFile($filename);

if (
    array_key_exists('owner', $_GET) && !empty($_GET['owner']) &&
    array_key_exists('minCall', $_GET) && !empty($_GET['minCall'])
) {
    $owner = $_GET['owner'];
    $minCall = (int)$_GET['minCall'];
    $abonents = filteredAbonents($abonents, $owner, $minCall);
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

        saveAbonentsToFile($filename, $abonents);
    }
}
