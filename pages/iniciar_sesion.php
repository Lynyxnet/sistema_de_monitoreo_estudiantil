<?php
    include_once "../db/conexion_pdo.php";
    session_start();

    if(isset($_POST['correo']) && isset($_POST['password']) || isset($_POST['matricula'])){
   
        //correo y password enviados desde el formulario
        $correo = $_POST['correo'];
        $password = $_POST['password'];

        //Usando marcadores de posicion con nombres `:nombre`
        $query = "SELECT * FROM usuarios WHERE correo = :correo AND password = :password";
        $stmt = $conn->prepare($query);
        $stmt->execute([':correo' => $correo, ':password' => $password]);
        $row = $stmt->fetch(); //Retorna un sola fila desde un resultado establecido como un array o objeto
        
        //To read back the value of a session variable, you can use echo/print statements, or var_dump() or print_r() functions.
        //var_dump($_SESSION);
        //print_r($row);

        //Comprobamos si hay datos en la variable $row de tipo arreglo que traje por medio de fetch()
        if(!empty($row)){
            $id_usuario = $row[0];
            $rol = $row[1]; //Obtener el valor del rol
            $nombre = $row[3];
            $apellido_paterno = $row[4];
            $apellido_materno = $row[5]; //Guardamos el valor de $row[5] en la variable $apellido_materno

            $_SESSION['rol'] = $rol;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['apellidoPaterno'] = $apellido_paterno;
            $_SESSION['apellidoMaterno'] = $apellido_materno; //Igaualamos el valor de $apellido_materno en la variable de $_SESSION['apellidoMaterno']

            //Evaluamos el "rol" con el que se identifica el usuario
            switch($_SESSION['rol']){
                case 0: //case 0, el super admin (jefa de carrera)
                    header('location: super_admin.php');
                break;
                case 1: //case 1, es un docente
                    header('location: docente.php');
                break;
                case 2: //case 2, es un usuario
                    header('location: usuario.php');
                break;
                default:
                    header('location: ../index.php');
                    exit();
                break;
            }

        } else {
          //echo "<script> alert('Usuario o contrasena invalidos') </script>";
          //echo "Usuario o contrasena invalidos";
          //echo '<div class="alert alert-warning" d-flex align-times-center role="alert"><strong>Error!</strong> Usuario o contrasena invalidos</div></div>';
          //header('Refresh: 1.5; url=../index.php');
          echo "<script> alert('Error! Usuario y contrasena son incorrectos o no existe'); window.location.href='../index.php'; </script>";
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Pagina Principal</title>

    <style>
      .navbar-brand {
        margin-left: 25px;
      }

      form > button {
        margin-top: 15px;
      }

      footer > div {
        color: white;
      }
    </style>
    
</head>
<body>