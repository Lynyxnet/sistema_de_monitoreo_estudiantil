<?php
$servername = "localhost";
$database = "monitoreo";
$username = "root";
$password = "";

try{
    $conn = new PDO("mysql:host=$servername;port=3307;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conectado exitosamente";
}catch(PDOException $e){
    echo "No hay conexion" . $e->getMessage();
    exit();
}

$db = null;
?>