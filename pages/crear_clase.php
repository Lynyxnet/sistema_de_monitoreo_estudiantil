<?php 
require '../vendor/autoload.php'; //Agregamos la libreria
// include_once "../db/conexion_pdo.php"; //Agregamos la conexion
// session_start();

// $materia = $_POST['nMateria'];
// $archivo = $_POST['archivo']; //Variable con el nombre del archivo //ejemplo.xlsx
// // otra rutina para obtener los datos d elso alumnos del archivo
// // re;iazo los isert de cada usuario para matricularlo en dicha materia
// // $matricula = $_POST['matricula'];
// $semestre = $_POST['semestre'];
// $fecha_inicio = $_POST['fechaInicio'];
// $fecha_final = $_POST['fechaFinal'];

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');


//if($stmt = $conn->prepare("SELECT * FROM usuarios WHERE matricula = :matricula")){
   // $stmt->execute([':matricula' => $matricula]);
    //$row = $stmt->fetch(); //Obtengo lo valores por medio del fecth y el array lo guardo en la variable $row que contiene todos los datos del usuario consultado
    //print_r($row);

    //if($row > 0){
        //$idusuario = $row['idUsuario']; //Al consultar por matricula, arrastramos lo datos obtenidos como el idUsuario de la matricula consultado y lo guardamos en la variable $idusuario

        // $query = "INSERT INTO materia (nombreMateria, semestre, fechaInicio, fechaFinal)
        // VALUES (:materia, :semestre, :fechaInicio, :fechaFinal)";
        // $stmt = $conn->prepare($query);
        // $stmt->execute([
        //     ':materia' => $materia,
        //     ':usuario' => $idusuario,
        //     'archivo' => $archivo,
        //     'semestre' => $semestre,
        //     'fechaInicio' => $fecha_inicio,
        //     'fechaFinal' => $fecha_final
        // ]);

        // echo "<script> alert('Creado exitosamente'); window.location.href='usuario.php'; </script>";
    //}
    

//}




?>