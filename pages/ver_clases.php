<?php 
include_once '../db/conexion_pdo.php';
session_start();

$pageid = $_GET['id']; //Aqui como parametro tengo "id", de esta forma obtengo el valor del id desde la url con el metodo GET
$sql = "SELECT * FROM materia WHERE idMateria = :clase";
$stmt = $conn->prepare($sql);
$stmt->execute([':clase' => $pageid]); //:clase es un placeholder quien espera un valor y sera igualado con la variable "$pageid" quien tien el valor del id que fue enviado por el usaurio atraves del metodo GET desde la URL
$pages = $stmt->fetch();
// print_r($pages); //imprime los valores de la query que es un array que contiene los valores traidos de la BD

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $pages['nombreMateria'] ?> </title>
</head>
<body>

<h1> <?php echo $pages['nombreMateria']?> </h1>
    
</body>
</html>