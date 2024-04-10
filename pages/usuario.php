<?php
include_once "../db/conexion_pdo.php";
session_start();
//no olvidar el dato "estado" para eliminar un dato, pero queda archivado por si queremos restaurarlo
// print_r($_SESSION);
//var_dump($_SESSION['rol']);
//print_r($_SESSION['rol']);

//print_r($usuario = $_SESSION['usuario']);
$usuario = $_SESSION['usuario'];

$sql = "SELECT * FROM materia WHERE idUsuario = :usuario";
$stmt = $conn->prepare($sql);
$stmt->execute([':usuario' => $usuario]);
// $pages = $stmt->fetchAll();
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

//print_r($pages);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <div class="container-fluid">
    
    <div class="navbar-brand" href="">Monitoreo estudiantil</div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <!-- <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"> < ?php echo $_SESSION['nombre'] ? > </a> -->
                    <a class="nav-link dropdown-toggle" id="user_user" data-bs-toggle="dropdown" href="#"> <?php echo $_SESSION['nombre']; ?> </a>
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

<main>
<div class="container-fluid text-center">
    <div class="row content">
        <div class="col-sm-1 sidenav">
            <p><a class="btn btn-outline-primary">Home</a></p>
            <p><a class="btn btn-outline-primary">Reportes</a></p>
        </div>

        
        <div class="col-sm-11">
          <div class="row">
            <h3>Grupos de asistencias</h3>
            <?php  foreach ($pages as $page): ?>

              <div class="video col-2">
                <h4> <?php echo $page['nombreMateria']; ?> </h4>
                <a href="ver_clases.php?id=<?php echo $page['idMateria']; ?>">Ver curso</a>
              </div>

            <?php endforeach; ?>
          </div>
        </div>

    </div>
</div>

</main>

<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center p-3">TSJZ - 2024 Copyright</div>
</footer>
</body>
</html>