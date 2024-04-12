<?php 

include_once "../db/conexion_pdo.php"; //Agregamos la conexion
session_start();
//print_r($_SESSION);
//echo "<br>";

$materia = $_POST['asignatura'];
$asignatura = ucwords($materia); //Convierte a mayusculas el primer caracter de cada palabra de una cadena
echo $asignatura;

$usuario = $_SESSION['usuario'];
//$archivo = $_POST['archivo']; //Variable con el nombre del archivo //ejemplo.xlsx
// otra rutina para obtener los datos d elso alumnos del archivo
// re;iazo los isert de cada usuario para matricularlo en dicha materia
// $matricula = $_POST['matricula'];
$semestre = $_POST['semestre'];
$fecha_inicio = $_POST['fechaInicio'];
$fecha_final = $_POST['fechaFinal'];

    $stmt = $conn->prepare("SELECT * FROM materia WHERE idUsuario = :idusuario AND nombreMateria = :asignatura");
    $stmt->execute([':idusuario' => $usuario, ':asignatura' => $asignatura]);
    $row = $stmt->fetch(); //Obtengo lo valores por medio del fecth y el array lo guardo en la variable $row que contiene todos los datos del usuario consultado
    
    if($row > 0){

        echo "<script> alert('Ya existe el curso en tu catalogo'); window.location.href='docente.php'; </script>";

    } else {
        //Crear curso
        // $idusuario = $row['idUsuario']; //Al consultar por matricula, arrastramos lo datos obtenidos como el idUsuario de la matricula consultado y lo guardamos en la variable $idusuario

        $query = "INSERT INTO materia (nombreMateria, idUsuario, semestre, fechaInicio, fechaFinal)
                  VALUES(:materia, :idusuario, :semestre, :fechaInicio, :fechaFinal)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':materia' => $asignatura,
            ':idusuario' => $usuario,
            'semestre' => $usuario,
            ':fechaInicio' => $fecha_inicio,
            ':fechaFinal' => $fecha_final
        ]);

        echo "<script> alert('Creado existosamente'); window.location.href='docente.php'; </script>";
    }

?>