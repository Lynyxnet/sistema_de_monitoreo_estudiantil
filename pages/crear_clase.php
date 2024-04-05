<?php
include_once "../db/conexion_pdo.php";
session_start();
//no olvidar el dato "estado" para eliminar un dato, pero queda archivado por si queremos restaurarlo
//print_r($_SESSION);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
      .navbar-brand {
        margin-left: 25px;
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
                    <!-- <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"> < ?php echo $_SESSION['nombre'] ? > </a> -->
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"> Hola, admin </a>
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


</body>
</html>