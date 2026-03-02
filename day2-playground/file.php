<?php
// 1- Open File
// $file = fopen("./data.txt", "w");
// var_dump($file);

$file = fopen(__DIR__ . "/data.txt", "w");


// 2- Write Data
$data = "Hello from ITI";
fwrite($file, $data);

// 3- Close File
fclose($file);