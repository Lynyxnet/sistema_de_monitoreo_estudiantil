<?php
include_once '../db/conexion_pdo.php';

if(!empty($_POST['matricula']) && !empty($_POST['id_materia'])){

$nombre = $_POST['nombre'];
$apellido_paterno = $_POST['apellidoPaterno'];
$apellido_materno = $_POST['apellidoMaterno'];
$matricula = $_POST['matricula'];
$correo = $_POST['correo'];
$id_materia = $_POST['id_materia'];

// VERIFICAMOS SI EXISTE LA MATRICULA DEL USUARIO EN LA BASE DE DATOS
$query_select = "SELECT * FROM usuarios WHERE matricula = :matricula";
$stmt_select = $conn->prepare($query_select);
$stmt_select->execute(['matricula' => $matricula]);
$row = $stmt_select->fetch(PDO::FETCH_ASSOC);
// print_r($row);

// Si existe el usuario, solo obtenemos el id del usuario y lo insertamos en la tabla materia_alumno para relacionarlo
if($row > 0){
    $id_usuario = $row['idUsuario']; //id del usuario

    $query_add = "INSERT INTO materia_alumno (idUsuario, idMateria)
                  VALUES (:id_usuario, :id_materia);
    ";
    $stmt_add = $conn->prepare($query_add);
    $stmt_add->execute([
        ':id_usuario' => $id_usuario,
        ':id_materia' => $id_materia
    ]);

    echo "<script> alert('Alumno agregado exitosamente'); window.location.href='ver_clase.php?id={$id_materia}'; </script>";
// Si no existe el usuario, el maestro puede agregar el nuevo alumno con lo datos requeridos, y relacionarlo con la materia automaticamente 
} else {

    $query_put = "INSERT INTO usuarios (idRole, matricula, nombre, apellidoPaterno, apellidoMaterno, password, correo)
                                 VALUES(2, :matricula, :nombre, :apellidoPaterno, :apellidoMaterno, 12345678, :correo)
                 ";
    $stmt_put = $conn->prepare($query_put);
    $stmt_put->execute([
        ':matricula' => $matricula,
        ':nombre' => $nombre,
        ':apellidoPaterno' => $apellido_paterno,
        'apellidoMaterno' => $apellido_materno,
        ':correo' => $correo
    ]);

    $last_id = $conn->lastInsertId(); //Obtener el id de usuario insertado recientemente
    // echo "Nuevo alumno agregado: " . $last_id;

    $query_introduce = "INSERT INTO materia_alumno (idUsuario, idMateria)
                        VALUES(:id_usuario, :id_materia)
    ";
    $stmt_introduce = $conn->prepare($query_introduce);
    $stmt_introduce->execute([
        ':id_usuario' => $last_id, 
        ':id_materia' => $id_materia
    ]);

    echo "<script> alert('Alumno agregado exitosamente'); window.location.href='ver_clase.php?id={$id_materia}'; </script>";

} //Fin del else

} //Final del if para agregar el nuevo alumno a la materia donde nos ubicamos

?>