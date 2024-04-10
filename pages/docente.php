<?php

include_once "../db/conexion_pdo.php";
session_start();
//no olvidar el dato "estado" para eliminar un dato, pero queda archivado por si queremos restaurarlo
// print_r($_SESSION);
//var_dump($_SESSION['rol']);
//print_r($_SESSION['rol']);

//print_r($usuario = $_SESSION['usuario']);
$usuario = $_SESSION['usuario'];

//Agregar otra querie donde solo filtre los cursos que solo imparte tal maestro independientemente

$sql = "SELECT * FROM materia";
$stmt = $conn->prepare($sql);
$stmt->execute();
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

      #user_admin{
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

<main>
<div class="container-fluid text-center">
    <div class="row content">
      
        <div class="col-sm-1 sidenav">
            <p><a class="btn btn-outline-primary">Home</a></p>
            <p><a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#crearClase">Crear clase</a></p>
            <p><a class="btn btn-outline-primary">Reportes</a></p>
            <p><a class="btn btn-outline-primary">Justificantes</a></p>
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

<!-- Crear clase -->
<div class="modal fade" id="crearClase" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content -->
        <div class="modal-content">
          <!-- Modal-header -->
          <div class="modal-header" style="padding:20px 50px;">
            <h4><span class="glyphicon glyphicon-lock">Crear clase</span></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <!-- Modal body -->
          <div class="modal-body" style="40px 50px;">
            
          <form action="crear_clase.php" method="post">
            <div class="row">
              <div class="col-12">
                <input type="text" class="form-control" name="maestro" placeholder="Docente">
              </div>
              <div class="space col-12">
                <input type="text" class="form-control" name="nMateria" placeholder="Asignatura">
              </div>
              <div class="space col-12">
                <input type="number" class="form-control" name="semestre" min="1" max="12" placeholder="Semestre">
              </div>

              <div class="row space align-items-center justify-content-center text-center" style="margin-bottom:15px">
                <div class="col-md-2">
                  <label for="diasInicioFin">Duracion</label>
                </div>
                <div class="col-md-5">
                  <input type="date" id="diasInicioFin" class="form-control" name="fechaInicio" placeholder="Fecha inicio">
                </div>
                <div class="col-md-5">
                  <input type="date" class="form-control" name="fechaFinal" placeholder="Fecha final">
                </div>
              </div>

            </div>
            </form>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="file" name="archivo" class="btn btn-primary">Importar archivo</button>
            <button type="button" name="borrarRegistro" class="btn btn-primary">Borrar</button>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
          </div>

        </div>
    </div>
</div>

</main>
    
</body>
</html>