<?php 
require_once '../db/conexion_pdo.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// var_dump($_POST['archivoExcel']);

if(isset($_POST['archivoExcel'])){
  // print_r($_POST['archivoExcel']);
  // echo "Si existe el archivo excel";
  $archivo = $_POST['archivoExcel'];

  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $spreadsheet = $reader->load($archivo);
  $excelSheet = $spreadsheet->getActiveSheet();
  $spreadSheetArray = $excelSheet->toArray();
  $sheetCount = count($spreadSheetArray);

  //Ponemos un -1 en la variable $sheetCount-1, sino contara otra fila de mas que no contiene ningun valor y saltara error 
  for($i=1; $i<=$sheetCount-1; $i++){
    //echo $spreadSheetArray[$i][0] . "<br>";
    //La filas seran recorridas con el for mediante la variable $i, y el valor de las columnas queda estatica en la columna 0
    $matricula = $spreadSheetArray[$i][0]; //esta variable guarda el array quien contiene lo valores de cada celda del excel obtenido mendiate el recorrido con el for(){}
    if(!empty($matricula)){
      $query_check = "SELECT COUNT(*)"


      //$query = "INSERT INTO usuarios (matricula) VALUES(:matricula)";
      //$stmt = $conn->prepare($query);
      //if($stmt->execute([':matricula' => $matricula])){
        echo "Exito! Datos del Excel importados a la base de datos";
      //}
      
    } else  {
      echo "Error en la insersion";
    }
  }

}else{
  echo "Problema al importar el archivo Excel";
}


// $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
// $reader->setReadDataOnly(true);
// $spreadsheet = $reader->load("archivo.xlsx");

// $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
// $data = $sheet->toArray();
// $sheetCount = count($data);
//print_r($data);

//Array multidimensional
// for($c=0; $c <= $sheetCount-1; $c++){
//   for($f=0; $f <= $sheetCount-1; $f++){
//     echo $data[$f][$c] . "<br>";
//   }
// }

?>

<!-- 
Array multidimensional

	     column 0 column1 column2 
row 0  x[0][0]	x[0][1] x[0][2]   
row 1  x[1][0]	x[1][1] x[1][2]
row 3  x[2][0]  x[2][1] x[2][2]

$myarray = array(
  array("Anna", "Luz", "Ivette"),
  array("Omar", "Emmanuel", "Hugo")
)

print_r("$myarray");
 -->

 <!DOCTYPE html>
 <html lang="en">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Importar un archivo excel</title>
 </head>
 <body>
    <form action="#" method="post">
      <input type="file" name="archivoExcel" id="filename" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"><br>
      <input type="submit" name="submit">
    </form>
 </body>
 </html>
