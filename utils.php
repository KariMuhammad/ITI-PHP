<?php

function readAllRecords(): array
{
    if (!file_exists(DATA_FILE) || filesize(DATA_FILE) === 0) {
        return [];
    }

    $records = [];
    $file = fopen(DATA_FILE, 'r');

    $headers = fgetcsv($file); // skip header row

    while (!feof($file)) {
        $row = fgetcsv($file);
        if ($row && count($row) === count($headers)) {
            $records[] = array_combine($headers, $row);
        }
    }

    fclose($file);
    return $records;
}

function writeAllRecords(array $records): void
{
    $file = fopen(DATA_FILE, 'w');

    // Write header row
    $headers = [
        'id',
        'firstname',
        'lastname',
        'country',
        'address',
        'gender',
        'skills',
        'username',
        'password',
        'department'
    ];
    fputcsv($file, $headers);

    foreach ($records as $record) {
        fputcsv($file, $record);
    }

    fclose($file);
}