<?php 
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load("archivo.xlsx");

$sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
$data = $sheet->toArray();
$sheetCount = count($data);
//print_r($data);

//Array multidimensional
for($i=2; $i <= $sheetCount; $i++){
    echo $data[$i][0];
}   

?>
