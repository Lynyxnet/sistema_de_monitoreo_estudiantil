<?php 
include_once '../db/conexion_pdo.php';
session_start();

$page_id = $_GET['id']; //Aqui como parametro tengo "id", de esta forma obtengo el valor del id desde la url con el metodo GET
$sql = "SELECT * FROM materia WHERE idMateria = :id_materia"; //consultamos idMateria con la variable $pageid
$stmt = $conn->prepare($sql);
$stmt->execute([':id_materia' => $page_id]); //:clase es un placeholder quien espera un valor y sera igualado con la variable "$pageid" quien tien el valor del id que fue enviado por el usaurio atraves del metodo GET desde la URL
$pages = $stmt->fetch();
// print_r($pages); //imprime los valores de la query que es un array que contiene los valores traidos de la BD

//debo obtener los datos mediante un inner join donde elijo las tablas con select
//ejemplo: tabla donde se junte materia-alumno arrastro la columna idalumno,nombrealumno, matricula de la tabla alumno
//arrastro la tabla materia, y dentro de la materia el profesor quien la imparte
//lo dias que estara y mostrarlo en la tabla

//Mostrar los alumnos de la materia con el ID que obtenemos por $_GET
$query_datos = "SELECT usuarios.matricula, usuarios.nombre
                FROM materia_alumno
                INNER JOIN usuarios ON materia_alumno.idUsuario = usuarios.idUsuario
                WHERE materia_alumno.idMateria = :page_id;
                ";
$stmt_datos = $conn->prepare($query_datos);
$stmt_datos->execute([':page_id' => $page_id]);
$results = $stmt_datos->fetchAll();
$numerosFilas = count($results);

$contador = 1; //Inicializamos el contador

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo $pages['nombreMateria'] ?> </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/limpiarDatosDocente.js"></script>

    <style>
        td, th {border: 1px solid #dddddd;}
    </style>

    <style>

    .container {
        margin-top: 20px;
    }

    .navbar-brand {
    margin-left: 25px;
    }

    #user_admin{
    font-weight: bold;
    }

    .space {
    margin-top: 9px;
    }

    .b-color {
    background-color: red;
    }
    
    </style>

</head>
<body>

<header>
<nav class="navbar navbar-expand-sm navbar-light bg-light">
      <div class="container-fluid">
        <div class="navbar-text" href="">Monitoreo estudiantil</div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="user_admin" data-bs-toggle="dropdown" href="#"><?php echo $_SESSION['nombre']; ?></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" class="dropdown-item">Ajustes</a>
                            <a href="cerrar_sesion.php" class="dropdown-item">Cerrar sesion</a>
                        </div>
                    </li>
                </ul>
            </div>
      </div>
</nav>
</header>

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
    <?php foreach ($results as $result): ?> <!--- inicio del foreach --->
    <tr>
        <td> <?php echo $contador; ?></td> <!--- Mostramos el contador --->
        <td> <?php echo $result['matricula']; ?> </td>
        <td> <?php echo $result['nombre'] ?> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <?php $contador++; ?> <!--- //Incrementamos el contador para siguiente fila --->
    <?php endforeach; ?> <!--- final del foreach --->
</table>
    
<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center p-3">TSJZ - 2024 Copyright</div>
</footer>
    
</body>
</html>