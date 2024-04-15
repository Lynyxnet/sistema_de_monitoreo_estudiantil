<?php 
include_once "../db/conexion_pdo.php"; //Agregamos la conexion
session_start();
//print_r($_SESSION);
//echo "<br>";
if(!empty($_POST['asignatura'] && !empty($_POST['semestre']))){ //Si es diferente de vacio entra en la condicional
    // var_dump($_POST['asignatura']);
    // print_r($_POST['submit']);
    $materia = $_POST['asignatura'];
    $asignatura = ucwords($materia); //Convierte a mayusculas el primer caracter de cada palabra de una cadena
    //print_r($_SESSION['usuario']);
    $usuario = $_SESSION['usuario'];
    //$archivo = $_POST['archivo']; //Variable con el nombre del archivo //ejemplo.xlsx
    // otra rutina para obtener los datos d elso alumnos del archivo
    // realiazo los insert de cada usuario para matricularlo en dicha materia
    // $matricula = $_POST['matricula'];
    $semestre = $_POST['semestre'];
    $fecha_inicio = $_POST['fechaInicio'];
    $fecha_final = $_POST['fechaFinal'];

        //Comprobar si existe el curso
        $stmt = $conn->prepare("SELECT * FROM materia WHERE idUsuario = :idusuario AND nombreMateria = :asignatura");
        $stmt->execute([':idusuario' => $usuario, ':asignatura' => $asignatura]);
        $row = $stmt->fetch(); //Obtengo lo valores por medio del fecth y el array lo guardo en la variable $row que contiene todos los datos del usuario consultado
    
        if($row > 0){

            echo "<script> alert('Ya existe el curso en tu catalogo'); window.location.href='docente.php'; </script>";

        } else {
            
            //Crear curso
            $query = "INSERT INTO materia (nombreMateria, idUsuario, semestre, fechaInicio, fechaFinal)
                    VALUES(:asignatura, :idusuario, :semestre, :fechaInicio, :fechaFinal)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':asignatura' => $asignatura,
                ':idusuario' => $usuario,
                'semestre' => $semestre,
                'fechaInicio' => $fecha_inicio,
                'fechaFinal' => $fecha_final
            ]);

            echo "<script> alert('Creado existosamente'); window.location.href='docente.php'; </script>";
        }
        
} elseif(!empty($_POST['archivoExcel'])){ //Si entra en la condicion es porque tiene el excel cargado, sino la condicion se rompe
    
    // echo "Enviado desde excel";

    

} else {
    
    echo "Por favor, llena el formulario!";

}


?>