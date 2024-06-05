<?php
include_once "../db/conexion_pdo.php";
session_start();
//No olvidar el dato "estado" para eliminar un dato, pero queda archivado por si queremos restaurarlo
//print_r($_SESSION);
//var_dump($_SESSION['rol']);
//print_r($_SESSION['rol']);

//print_r($usuario = $_SESSION['usuario']);
$id_usuario = $_SESSION['id_usuario']; //El id del usuario

$sql = "SELECT * FROM materia WHERE idUsuario = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->execute([':id_usuario' => $id_usuario]);
$pages = $stmt->fetch();
// print_r($pages);

//CONSULTAR materia_alumno, para obtener la materia donde esta relacionado
//La tabla materia_alumno relaciona el alumno con la materia
$query_select = "SELECT materia.idMateria, materia.nombreMateria 
FROM materia_alumno
INNER JOIN materia ON materia.idMateria = materia_alumno.idMateria
WHERE materia_alumno.idUsuario = :id_usuario";
$stmt_select = $conn->prepare($query_select);
$stmt_select->execute([
  'id_usuario' => $id_usuario
]);
$rows = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
$paginas = $rows;
// print_r($rows);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/usuario.css">
    <style>
      .container {
        margin-top: 20px;
      }

      .navbar-brand {
        margin-left: 25px;
      }

      #user_user{
        font-weight: bold;
      }

      .space {
        margin-top: 9px;
      }
    </style>
</head>
<body>

<header>
<nav class="navbar navbar-expand-sm navbar-light"  style="background-color: #82E0AA;">
      <div class="container-fluid">
          <div class="navbar-text"> <a href="#" style="text-decoration: none;"> <i class="fas fa-graduation-cap"></i> <strong class="text-black" style="margin-left:3px;">Sistema de monitoreo estudiantil</strong> </a></div>
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

<main> <!-- Inicio del main -->

<!-- Grupos de asistencia -->
<div class="container-fluid">

    <div class="row">
      <div class="col-12 text-center" style="background-color: #AED6F1;">
        <h3><strong>Grupos de asistencias</strong></h3>
      </div>
    </div>

    <div class="row space_d" style="background-color: #;">

      <!-- 1/2 columna -->
      <div class="col-1" style="margin-left: 10px;">
        <div class="row">
          <div class="col">
            <button type="button" class="btn btn-primary mb-2 small-column_d">Home</button>
            <button type="button" class="btn btn-primary mb-2 small-column_d">Reportes</button>
          </div>
        </div>
      </div>

      <!-- 2/2 columna -->
      <div class="col" style="background-color: #; margin-left: 10px;">
        <div class="row" style="width: 1200px;">
          <!-- Segunda columna que abarca todo -->
          <div class="col" style="height: 400px; overflow-y: auto;">
            
            <div class="row">

              <?php foreach ($paginas as $pagina): ?>
                <div class="col-2">
                  <a href="ver_clase_alumno.php?id=<?php echo $pagina['idMateria']; ?>" class="card-link">
                    <div class="card custom-card mb-3">
                      <img class="card-img-top">
                      <div class="card-body">
                        <h6 class="card-title text-center"> <?php echo $pagina['nombreMateria']; ?> </h6>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
              
            </div>

          </div>
        </div>
      </div>

    </div>

</div>

</main> <!--- Final del main --->

<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center p-2">TSJZ - 2024 Copyright</div>
</footer>

</body>
</html>