<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load('mockdata_dupak.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

echo "ROW 102 (index 102):\n";
print_r($data[102]);

echo "\n\nROW 109:\n";
print_r($data[109]);
