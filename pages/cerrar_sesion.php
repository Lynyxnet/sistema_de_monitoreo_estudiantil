<?php 

session_start();
session_destroy();

//REDIRECCIONAR AL USUARIO
header("Location: ../index.php");

?>