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
    
    $success = false;

    if(!empty($matricula)){
      $query_check = "SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula";
      $stmt_check = $conn->prepare($query_check);
      $stmt_check->execute([':matricula' => $matricula]);
      $count = $stmt_check->fetchColumn();
        if($count > 0){
          echo "La matricula ya existe en la base de datos";
        } else {
            $query_insert = "INSERT INTO usuarios (matricula) VALUES(:matricula)";
            $stmt_insert = $conn->prepare($query_insert);
              if($stmt->execute([':matricula' => $matricula])){
                // echo "Exito! Datos del Excel importados a la base de datos";
                $success = true;
              } else {
                echo "Error en la insersion";
              }
        }
    } else {
      echo "Problemas al importar el archivo Excel";
    }
  }
}

?>