<?php
include_once "../db/conexion_pdo.php";

//Datos obtenidos desde el formulario donde el usuario crea un nuevo usuario
//mysql_real_escape_string(&_POST['...']);
if(!empty($_POST['correo']) && !empty($_POST['password_1'])){ //Verificamos si el correo y contrasena estan seteado (si hay datos enviados)
    //$correo = $_POST['correo'];

    //Con esta consulta verificamos si el usuario que vamos a crear ya existe en la base de datos
    if($stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :correo")){ //:correo es un placeholder esperando un valor
       $stmt->execute([':correo' => $_POST['correo']]);
       $row = $stmt->fetch(); //Verifica en la columnas seleccionadas si existe la cuenta dentro de la base de datos
       
       if($row > 0){ //recorre el array, sino es mayor a cero se rompe, porque no existe valor, asi que se ejecuta el else donde creamos el nuevo usuario
          
          //Mensaje que alerta al usuario que ya existe el usuario en la base de datos
          echo "<script>alert('Usuario existe, por favor elige otra cuenta'); window.location.href='../index.php'; </script>";

       } else {
          //Crear cuenta
          //Ejecutar codigo para crear nueva cuenta
          if($_POST['password_1'] == $_POST['password_2']){ //Comprobamos si la contrasena 1 y contrasena 2 son iguales, si si, se ejecuta la creacion de la cuenta
    
            //Obtenemos y guardamos los datos obtenidos desde el formulario
            $rol = $_POST['roles'];
            $matricula = $_POST["matricula"];
            $nombre = $_POST['nombre'];
            $apellido_Paterno = $_POST['apellidoPaterno'];
            $apellido_Materno = $_POST['apellidoMaterno'];
            $password = $_POST['password_1']; //Se guarda en la variable "$password_1"
            //$password_hashed = password_hash($password, PASSWORD_DEFAULT); //Se encripta en md5 para que no sea descubierta la contrasena
            $correo = $_POST["correo"];
       
            //echo $nombre . "<br>" . $apellido_Paterno . "<br>" . $apellido_Materno . "<br>" . $matricula . "<br>" . $correo . "<br>" . $rol . "<br><br>";
     
            $query = "INSERT INTO usuarios (idRole, matricula, nombre, apellidoPaterno, apellidoMaterno, password, correo)
            VALUES (:rol, :matricula, :nombre, :apellidoPaterno, :apellidoMaterno, :password, :correo)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
            ':rol' => $rol,
            ':matricula' => $matricula, 
            ':nombre' => $nombre, 
            ':apellidoPaterno' => $apellido_Paterno, 
            ':apellidoMaterno' => $apellido_Materno,
            ':password' => $password,
            ':correo' => $correo]);
    
            echo "<script> alert('Creado exitosamente'); window.location.href='../index.php'; </script>";
            //$stmt->close();

          } else {
            echo "<script> alert('Las contrasenas no son iguales, intenta de nuevo!'); window.location.href='../index.php'; </script>";
          }

       }
    
    } else {
      echo "Error al momento de consulta informacion!";
    }
    //$conn->close();

} else {
  echo "<script> alert('Error! Por favor completa el formulario'); window.location.href='../index.php'; </script>";
}

?>