<?php 
include_once '../db/conexion_pdo.php';
session_start();

$page_id = $_GET['id']; //Aqui como parametro tengo "id" de la materia que fue enviado como link, de esta forma obtengo el valor del id de la materia desde la url con el metodo GET
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
$query_datos = "SELECT usuarios.matricula, usuarios.nombre, usuarios.correo
                FROM materia_alumno
                INNER JOIN usuarios ON materia_alumno.idUsuario = usuarios.idUsuario
                WHERE materia_alumno.idMateria = :page_id;
                ";
$stmt_datos = $conn->prepare($query_datos);
$stmt_datos->execute([':page_id' => $page_id]);
$results = $stmt_datos->fetchAll();
$numerosFilas = count($results);

$contador = 1; //Inicializamos el contador
$count = 1; //Incializamos el contador

//CONSULTAR MES MINIMO Y MES MAXIMO DE LA MATERIA
$query_mes = "SELECT
              MIN(DATE_FORMAT(fecha, '%b')) AS mesInicio,
              MAX(DATE_FORMAT(fecha, '%b')) AS mesFinal,
              DATE_FORMAT(fecha, '%Y') AS year
              FROM materia_dia
              WHERE idMateria = :id_materia
              ";
$stmt_mes = $conn->prepare($query_mes);
$stmt_mes->execute([':id_materia' => $page_id]);
$meses = $stmt_mes->fetch();

$mes_inicio = $meses['mesInicio'];
$mes_final = $meses['mesFinal'];
$year = $meses['year'];
// FIN DE LA CONSULTA DE MES INICIO Y FINAL

// ELIMINAR EL ALUMNO SELECCIONADO
if(!empty($_POST['eliminarAlumnos'])){
  $editarAlumnos = $_POST['eliminarAlumnos'];
  // echo var_dump($editarAlumno);
  
  foreach($editarAlumnos as $key => $editarAlumno){
    // echo var_dump($editarAlumno);
    $matricula = (string)$key;

    // echo $key;
    // echo gettype($key);
    // echo $matricula;
    // echo gettype($key);

    $query_select = "SELECT idUsuario FROM usuarios WHERE matricula = :matricula";
    $stmt_select = $conn->prepare($query_select);
    $stmt_select->execute([':matricula' => $matricula]);
    $row = $stmt_select->fetch(PDO::FETCH_ASSOC);
    // echo var_dump($row['idUsuario']);

    if($row > 0){
      $id_usuario = $row['idUsuario'];
      // echo $id_usuario;
      $query_delete = " DELETE FROM materia_alumno WHERE idMateria = :id_materia AND idUsuario = :id_usuario";
      $stmt_delete = $conn->prepare($query_delete);
      $stmt_delete->execute([':id_materia' => $page_id, ':id_usuario' => $id_usuario]);
      // $row = $stmt_delete->fetch(PDO::FETCH_ASSOC);
    }
 
  }
  
  echo "<script> alert('Alumno eliminado exitosamente'); window.location.href='ver_clase.php?id={$page_id}'; </script>";

}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/ver_clase.css">
</head>
<body>

<div>
<!-- Navbar -  Titulo de la pagina y profile user -->
<header>
  <nav class="navbar navbar-expand-sm navbar-light wallpaper-background">
      <div class="container-fluid">
        <div class="navbar-text"><a href="docente.php"> <i class="fas fa-graduation-cap"></i> <strong class="text-black" style="margin-left:3px;">Sistema de monitoreo estudiantil</strong></a></div>
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
      <input type="text" value="<?php echo $pages['semestre']?>" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
    <div class="col-2 d-flex align-items-center">
      <strong class="me-2">Meses: </strong>
      <input type="text" value="<?php echo $mes_inicio . '-' . $mes_final ?>" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
    <div class="col-1 d-flex align-items-center">
      <strong class="me-2">Año: </strong>
      <input type="text" value="<?php echo $year ?>" class="remove-styles-input form-control form-control-sm" disabled readonly>
    </div>
  </div> <!--- Termino del row-->
</div>

<!-- Botones desplegables (home,cursos,reportes) y lista de los alumnos -->
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
          <tr class="text-center">
            <th>#</th>
            <th>Matricula</th>
            <th>Nombre completo</th>
            <th colspan="1">Asistencias</th>
            <th colspan="1">Enero</th>
            <th colspan="1">Asistio</th>
            <th colspan="1" class="text-center small-column">Asistencias</th>
            <th colspan="1" class="text-center small-column">Faltas</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $result): ?> <!--- inicio del foreach --->
          <tr>
            <td class="text-center"> <?php echo $contador; ?> </td> <!--- Mostramos el contador --->
            <td class="text-center"> <?php echo $result['matricula']; ?> </td>
            <td> <?php echo $result['nombre'] ?> </td>
            <?php $contador++; ?> <!--- //Incrementamos el contador para siguiente fila --->
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          <?php endforeach; ?> <!--- final del foreach --->
          </tr>
          </tbody>
        <tdbody>
        </tbody>
        </table>
    </div>

  </div> 
</div>

<!-- Modal - Dove vemos las opciones para eliminar/editar alumnos -->
<div class="modal fade modal-xl" id="editarAlumnos" role="dialog" style="height: 900px;">
    <div class="modal-dialog">
        <!-- Modal content -->
        <div class="modal-content">
          <!-- Modal-header -->
          <div class="modal-header">
            <h4><strong>Editar alumnos</strong></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          
          <!-- Modal body - Agregar alumnos -->
            <div class="modal-body">
              
              <div class="container-fluid" style="margin: 8px 0px;">
                <div class="row">
                  <div class="col d-flex align-items-center justify-content-end">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#agregarAlumno">Agregar alumno</button>
                  </div>
                </div>
              </div>
              
              <div class="table-container-aa">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col" class="text-center"">#</th>
                    <th scope="col" class="text-center">Matricula</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido paterno</th>
                    <th scope="col">Apellido materno</th>
                    <th scope="col" class="text-center">Correo</th>
                    <th scope="col" class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody style="background-color: blue;">
                
                <form action="#" method="post"> <!--- Inicio del form ---->
                    <tr>
                    <?php foreach($results as $result) { ?>
                      <td class="text-center"> <?php echo $count; ?> </td>
                      <td 
                      class="text-center"> <?php echo $result['matricula'] ?>
                      <input type='hidden' name='matriculas[]' value='<?php echo $result['matricula']; ?>' >
                      </td>
                      <td> <?php echo $result['nombre']; ?> </td>
                      <td></td>
                      <td class="text-center"><td> <?php echo $result['correo']?> 
                      </td></td>
                      <?php $count++;?>
                      <td class="text-center">
                        <!-- Boton editar -->
                        <button type="submit" data-bs-toggle="modal" data-bs-target="#editarDatosAlumnos" data-matricula="<?php echo $result['matricula']; ?>">
                          <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <!-- Boton para eliminar el alumno que es seleccionado al momento de dar click -->
                        <button type="submit" name="eliminarAlumnos[<?php echo $result['matricula']; ?>]" value="eliminar">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                    <?php } ?>
                </form>

                </tbody>
              </table>
              </div>
 
            <!-- Modal footer -->
            <div class="modal-footer">
              <!-- <button type="button" onclick="limpiarDatos()" class="btn btn-primary">Borrar todos</button> -->
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

            </div>

          </form>

        </div>
    </div>
</div>

<!-- Sub-modal - Editar datos del alumno -->

<!-- Sub-modal - Agregar alumno a la lista de asistencia -->
<div class="modal fade" id="agregarAlumno" role="dialog">
  <div class="modal-dialog modal-dialog-centered" style="width: 600px;">
      <!-- Modal content -->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Agregar alumno</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

      <form action="agregar_alumno.php" method="post" enctype="multipart/form-data">

          <div class="row">
            <div class="col-12">
              <!-- input oculto pero tiene el valor de id_materia que enviaremos via formulario -->
              <!-- Enviamos el id de la materia que ya habiamos obtenido mendiante la url -->
              <input type="hidden" name="id_materia" value="<?php echo htmlspecialchars($page_id); ?>">
            </div>
            <div class="col-12 space-info">
              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
            </div>
            <div class="col-6 space-info">
              <input type="text" class="form-control" id="apellido_paterno" name="apellidoPaterno" placeholder="Apellido paterno" required>
            </div>
            <div class="col-6 space-info">
              <input type="text" class="form-control" id="apellido_materno" name="apellidoMaterno" placeholder="Apellido materno" required>
            </div>
            <div class="col-4 space-info">
              <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Matricula" required>
            </div>
            <div class="col-8 space-info">
              <input type="text" class="form-control" id="correo" name="correo" placeholder="Correo" required>
            </div>
          </div>

      </div> <!--- Final del modal-body --->
      
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Limpiar</button>
        <button type="submit" id="submit" name="submit" class="btn btn-success">Aceptar</button>
      </div>
      <form>

    </div>
  </div>
</div>

</main> <!--- Fin del main --->

<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center text-white p-2">TSJZ - 2024 Copyright</div>
</footer>

<!-- Script para cerrar el primer modal para abrir el segundo modal -->
<script>
    // Cerrar el primer modal cuando se abre el segundo
    $('#myModal').on('show.bs.modal', function (e) {
        $('#editarAlumnos').modal('hide');
    });
</script>

</body>
</html>