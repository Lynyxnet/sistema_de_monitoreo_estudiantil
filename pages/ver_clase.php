<?php 
include_once '../db/conexion_pdo.php';
include_once 'editar_alumnos.php';
session_start();

$page_id = $_GET['id']; //Aqui como parametro tengo "id", de esta forma obtengo el valor del id desde la url con el metodo GET
$sql = "SELECT * FROM materia WHERE idMateria = :id_materia"; //consultamos idMateria con la variable $pageid
$stmt = $conn->prepare($sql);
$stmt->execute([':id_materia' => $page_id]); //:clase es un placeholder quien espera un valor y sera igualado con la variable "$pageid" quien tien el valor del id que fue enviado por el usaurio atraves del metodo GET desde la URL
$pages = $stmt->fetch(PDO::FETCH_ASSOC);
// print_r($pages); //imprime los valores de la query que es un array que contiene los valores traidos de la BD

//debo obtener los datos mediante un inner join donde elijo las tablas con select
//ejemplo: tabla donde se junte materia-alumno arrastro la columna idalumno,nombrealumno, matricula de la tabla alumno
//arrastro la tabla materia, y dentro de la materia el profesor quien la imparte
//lo dias que estara y mostrarlo en la tabla

//Mostrar los alumnos de la materia con el id que obtenemos por $_GET
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

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../js/limpiarDatosDocente.js"></script>
    <link rel="stylesheet" href="../css/ver_clase.css">
</head>
<body>

<div>
<!-- Navbar -  Titulo de la pagina y profile user -->
<header>
  <nav class="navbar navbar-expand-sm navbar-light wallpaper-background">
      <div class="container-fluid">
        <div class="navbar-text"><a href="docente.php"><strong class="text-black">Monitoreo estudiantil</strong></a></div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><strong class="text-black"><?php echo $_SESSION['nombre']; ?></strong></a>
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

<!-- Inicio del main -->
<main>
<!-- Titulo de la materia junto con botones (imprimir, editar alumnos) -->
<div class="container-fluid wallpaper-sub-background">
  <div class="row">
    <div class="col-8 d-flex bg-warning align-items-center"><h3 class="text-center"> <?php echo $pages['nombreMateria']?></h3></div>
    <!-- Columna anidada -->
    <div class="col-4 d-flex align-items-center bg-warning justify-content-end">
      <button type="button" class="btn btn-light btn-sm me-2">Imprimir</button>
      <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editarAlumnos">Editar alumnos</button>
    </div>
  </div>
</div>

<!-- Informacion del maestro y materia -->
<div class="container-fluid wallpaper-sub-background space">
  <div class="row">
    <!-- Primer info input -->
    <div class="col-3 d-flex align-items-center">
      <strong class="me-2">Docente: </strong>
      <input type="text" value="<?php echo $_SESSION['nombre'] . " " . $_SESSION['apellidoPaterno']; ?>" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
    <div class="col-3 d-flex align-items-center">
      <strong class="me-2">Asignatura: </strong>
      <input type="text" value="<?php echo $pages['nombreMateria']; ?>" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
    <div class="col-2 d-flex align-items-center">
      <strong class="me-2">Semestre: </strong>
      <input type="text" value="6" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
    <div class="col-2 d-flex align-items-center">
      <strong class="me-2">Mes: </strong>
      <input type="text" value="Enero" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
    <div class="col-1 d-flex align-items-center">
      <strong class="me-">Año: </strong>
      <input type="text" value="2024" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
  </div> <!--- Termino del row-->
</div>

<!-- Botones desplegables (hombe,cursos,reportes) y lista de los alumnos -->
<div class="container-fluid">
  <div class="row"> <!-- Dentro del row tenemos dos columnas -->
    
    <!-- Primer div -->
    <div class="col-1 space">
        <div class="row">
          <div class="col">
            <button type="button" class="btn btn-outline-success mb-2 small-column"><a href="#">Home</a></button>
            <button type="button" class="btn btn-outline-success mb-2 small-column"><a href="docente.php">Cursos</a></button>
            <button type="button" class="btn btn-outline-success mb-2 small-column">Reportes</button>
          </div>
        </div>
    </div>

    <!-- Segundo div -->
    <div class="col-11 table-container space">
        <table class="table table-striped">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th class="text-center">Matricula</th>
            <th class="text-center">Nombre completo</th>
            <th colspan="6" class="text-center">Asistencias</th>
            <th colspan="6" class="text-center">Enero</th>
            <th colspan="1" class="text-center small-column">Asistencias</th>
           <th colspan="1" class="text-center small-column">Faltas</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $result): ?> <!--- inicio del foreach --->
          <tr>
            <td class="text-center"> <?php echo $contador; ?></td> <!--- Mostramos el contador --->
            <td class="text-center"> <?php echo $result['matricula']; ?> </td>
            <td> <?php echo $result['nombre'] ?> </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <?php $contador++; ?> <!--- //Incrementamos el contador para siguiente fila --->
          <?php endforeach; ?> <!--- final del foreach --->
          </tbody>

          </table>
    </div>

  </div> 
</div>

<!-- Modal - Editar alumnos -->
<div class="text-center">
  <div class="modal fade modal-lg" id="editarAlumnos" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content -->
        <div class="modal-content">
          <!-- Modal-header -->
          <div class="modal-header" style="padding:20px 50px;">
            <h4><span class="glyphicon glyphicon-lock">Editar alumnos</span></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <form action="crear_clase.php" method="post" enctype="multipart/form-data">
          
            <!-- Modal body - Agregar alumnos -->
            <div class="modal-body">

              <div class="container-fluid" style="margin: 8px 0px;">
                <div class="row">
                  <div class="col d-flex align-items-center justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm">Agregar alumno</button>
                  </div>
                </div>
              </div>

              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Matricula</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido paterno</th>
                    <th scope="col">Apellido materno</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>
                      <i class="fas fa-edit"></i>
                      <i class="far fa-trash-alt"></i>
                    </th>
                  </tr>
                </tbody>
              </table>

            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
              <!-- <button type="button" onclick="limpiarDatos()" class="btn btn-primary">Borrar todos</button> -->
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

          </form>


        </div>
    </div>
  </div>
</div>
</main>

<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center text-white p-2">TSJZ - 2024 Copyright</div>
</footer>
    
</body>
</html>