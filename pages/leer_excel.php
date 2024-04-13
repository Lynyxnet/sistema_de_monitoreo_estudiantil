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
    //$success = false; // Variable para controlar el éxito del insert

    if(!empty($matricula)){
      $query_check = "SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula";
      $stmt_check = $conn->prepare($query_check);
      $stmt_check->execute([':matricula' => $matricula]);
      $count = $stmt_check->fetchColumn();

      if($count > 0){
        $success = false;
        //echo "La matricula ya existe en la base de datos";
      } else {
        $query_insert = "INSERT INTO usuarios (matricula) VALUES (:matricula)";
        $stmt_insert = $conn->prepare($query_insert);
          if($stmt_insert->execute([':matricula' => $matricula])){
            $success = true;
            //echo "Datos del excel insertado";
          }
      }
      
    }

  }

  if($success == true){
    //var_dump($success);
    echo "Datos del Excel insertado";
  } elseif($success == false) {
    //var_dump($success);
    echo "La matricula ya existe en la base de datos";
  }

} else {
  echo "Problemas al importar el archivo Excel";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Importar archivo excel</title>
</head>
<body>
  <form action="#" method="post">
    <input type="file"  name="archivoExcel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"><br>
    <input type="submit" name="submit">
  </form>
</body>
</html>