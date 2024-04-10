<?php 
include_once '../db/conexion_pdo.php';
session_start();

$pageid = $_GET['id']; //Aqui como parametro tengo "id", de esta forma obtengo el valor del id desde la url con el metodo GET
$sql = "SELECT * FROM materia WHERE idMateria = :idmateria"; //consultamos idMateria con la variable $pageid
$stmt = $conn->prepare($sql);
$stmt->execute([':idmateria' => $pageid]); //:clase es un placeholder quien espera un valor y sera igualado con la variable "$pageid" quien tien el valor del id que fue enviado por el usaurio atraves del metodo GET desde la URL
$pages = $stmt->fetch();
// print_r($pages); //imprime los valores de la query que es un array que contiene los valores traidos de la BD

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $pages['nombreMateria'] ?> </title>
    <style>
        td, th {border: 1px solid #dddddd;}
    </style>
</head>
<body>

<h1> <?php echo $pages['nombreMateria']?> </h1>

<table>
    <tr>
        <th>No. Orden</th>
        <th>Matricula</th>
        <th>Nombre completo</th>
        <th>Mes</th>
        <th>Niveles</th>
        <th>Asistencias</th>
        <th>Faltas</th>
        <th>Justicaciones</th>
    </tr>
    <tr>
        <td>1</td>
        <td>17011650</td>
        <td>Ivette</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
    
</body>
</html>