<?php 

session_start();
session_unset(); //Elimina todas las variables de sesion
session_destroy(); //Destruir la sesion

//REDIRECCIONAR AL USUARIO
header("Location: ../index.php");
exit;

?>