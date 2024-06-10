<?php 
include_once '../db/conexion_pdo.php';
session_start();

//id del alumno
$id_usuario = $_SESSION['id_usuario'];

//CONSULTAR INFORMACION DE LA MATERIA Y OBTENER EL ID DEL MAESTRO
$page_id = $_GET['id'];
$sql = "SELECT
        materia.idUsuario,
        materia.nombreMateria,
        materia.semestre,
        materia.fechaInicio,
        materia.fechaFinal
        FROM materia
        JOIN materia_alumno ON materia.idMateria = materia_alumno.idMateria
        WHERE materia_alumno.idMateria = :id_materia"; //consultamos idMateria con la variable $pageid
$stmt = $conn->prepare($sql);
$stmt->execute([':id_materia' => $page_id]); //:clase es un placeholder quien espera un valor y sera igualado con la variable "$pageid" quien tien el valor del id que fue enviado por el usaurio atraves del metodo GET desde la URL
$pages = $stmt->fetch(PDO::FETCH_ASSOC);
// print_r($pages);

// MES INICIO
$mesInicio = date_create($pages['fechaInicio']);
$mes_inicio = date_format($mesInicio, 'M');
// MES FINAL
$mesFinal = date_create($pages['fechaFinal']);
$mes_final = date_format($mesFinal, 'M');
// YEAR
$year = date_format($mesInicio, 'Y');

// CONSULTAR ID DEL MAESTRO
// id del maestro
$id_maestro = $pages['idUsuario'];

  // CONSULTAR LA INFO DEL MAESTRO
  $query_check = "SELECT
                nombre,
                apellidoPaterno,
                apellidoMaterno
                FROM usuarios
                WHERE idUsuario = :id_maestro";
  $stmt_check = $conn->prepare($query_check);
  $stmt_check->execute([':id_maestro' => $id_maestro]);
  $results = $stmt_check->fetch();
  // print_r($results);

  //CONSULTAR MES MINIMO Y MES MAXIMO DE LA MATERIA
  $query_mes = "SELECT
              DATE_FORMAT(MIN(fecha), '%b') AS mesInicio,
              DATE_FORMAT(MAX(fecha), '%b') AS mesFinal,
              DATE_FORMAT(fecha, '%Y') AS year
              FROM materia_dia
              WHERE idMateria = :id_materia
              ";
    $stmt_mes = $conn->prepare($query_mes);
    $stmt_mes->execute([':id_materia' => $page_id]);
    $meses = $stmt_mes->fetch();
    // print_r($meses);

    $mes_inicio = $meses['mesInicio'];
    $mes_final = $meses['mesFinal'];

    // CONVERTIR EL MES INICIO Y FINAL EN NUMEROS
    $mesInicio = date("n", strtotime($mes_inicio));
    $mesFinal = date("n", strtotime($mes_final));

  // TRADUCIR LOS MESES DE INGLES AL ESPANOL
  $spanishMonth = array(
  1 => "Enero", 
  2 => "Febrero", 
  3 => "Marzo", 
  4 => "Abril", 
  5 => "Mayo", 
  6 => "Junio",
  7 => "Julio",
  8 => "Agosto",
  9 => "Septiembre",
  10 => "Octubre",
  11 => "Noviembre",
  12 => "Diciembre"
  );

  // OBTENER EL MES ACTUAL O EL SELECCIONADO
  if (isset($_POST['mesActual'])) {
    $mesActual = $_POST['mesActual'];
  } else {
    $mesActual = 1; // Empezar en el mes 1 (Enero)
  }
  
  // Decrementar el mes
  if(isset($_POST['decrementarMes'])) {
    $mesActual--;
    if ($mesActual < $mesInicio) {
        $mesActual = $mesFinal;
    }
  }
  // Incrementar el mes
  if(isset($_POST['incrementarMes'])) {
    $mesActual++;
    if ($mesActual > $mesFinal) {
        $mesActual = $mesInicio;
    }
  }

  //OBTENER LOS DIAS DEL MES SELECCIONADO
  //INCREMENTAR LOS DIAS
  if (isset($_POST['selected_day'])) {
    $selectedDay = $_POST['selected_day'];
  } else {
  $selectedDay = 1; // Empezar en el dia de la semana 1 (Monday)
  }

  if(isset($_POST['prev_day'])){
    $selectedDay--;
    if($selectedDay < 1){
      $selectedDay = 6;
    }
  } elseif(isset($_POST['next_day'])){
      $selectedDay++;
    if($selectedDay > 6){
      $selectedDay = 1;
    }
  }

    //CONSULTAMOS LAS ASISTENCIAS DEL USUARIO QUE INICIO SESION
    $query_audit = "SELECT
                      asistencias.asistencia,
                      materia_dia.diaSemana,
                      materia_dia.fecha
                    FROM asistencias
                    JOIN materia_dia ON asistencias.idMateriaDia = materia_dia.idMateriaDia
                    WHERE asistencias.idUsuario = :id_usuario AND materia_dia.idMateria = :id_materia";
      $stmt_audit = $conn->prepare($query_audit);
      $stmt_audit->execute([':id_usuario' => $id_usuario, ':id_materia' => $page_id]);
      $outputs = $stmt_audit->fetchAll();
      //echo var_dump($outputs);
      //Para apaarecer los datos tiene que haber asistencia en la materia y usuario relacionado

      //Desglosamos dia de la semana(index head) -> fecha con su asistencia
      $asistenciaAlumno = [];
      foreach($outputs as $output){
      $dia_semana = $output['diaSemana']; //Dia de la semana
      $fecha = $output['fecha'];

      $date = new DateTime($output['fecha']);
      $monthSelected = $date->format('n'); //Convertimos la fecha en un numero del dia del mes

      $estadoAsistencia = $output['asistencia']; //Asistio o falto
      
      if($monthSelected == $mesActual){
        //Indice -> fechas con su asistencia (asistio o falto)
        $asistenciaAlumno[$dia_semana][$fecha] = $estadoAsistencia;
      }

      // foreach($asistenciaAlumno as $diaSemana => $asistenciaPorDia){
      //   // echo $diaSemana . "<br>"; //Lunes, martes, miercoles...
      //   foreach($asistenciaPorDia as $fecha => $estadoAsistencia){
      //     $fecha .  " " . $estadoAsistencia . "<br>"; //fecha 0000/00/00 -> asistio/falto
      //   }
      // }
      // Dia semana (lunes, martes, miercoles, jueves, viernes, sabado) = >>> fecha >>> falto o asistio
    }

//TRADUCIR LOS MESES AL ESPANOL
$spanishMonth[$mesActual];

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../js/limpiarDatosDocente.js"></script>
    <script src="../js/OcultarAlertBox.js"></script>
    <link rel="stylesheet" href="../css/ver_clase1.css">
    <style>
      .scroll-container {
        width: 100%;
        overflow-x: auto;
        white-space: nowrap; /* Para evitar que el contenido se rompa en múltiples líneas */
        -ms-overflow-style: none;  /* Internet Explorer 10+ */
        scrollbar-width: none;  /* Firefox */
        cursor: grab;
        font-size: 12px;
        border: 1px solid;
      }

      /* Para navegadores basados en WebKit (Chrome, Safari) */
      .scroll-container::-webkit-scrollbar {
        display: none;
      }

      .date-table {
        display: inline-block; /* Asegura que la tabla no se rompa en múltiples líneas */
      }

      .date-table > th, td {
        text-align: left;
      }
    </style>
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

<main> <!-- *** Inicio del main *** -->

<!-- Titulo de la materia junto con botones (imprimir, editar alumnos) -->
<div class="container-fluid wallpaper-sub-background">
  <div class="row">
    <div class="col-8 d-flex bg-warning align-items-center"><h3 class="text-center"> <?php echo $pages['nombreMateria']?></h3></div>
    <!-- Columna anidada -->
    <div class="col-4 d-flex align-items-center bg-warning justify-content-end">
      <button type="button" class="btn btn-outline-dark btn-sm me-2">Imprimir</button>
    </div>
  </div>
</div>

<!-- Informacion del maestro y materia -->
<div class="container-fluid wallpaper-sub-background space">
  <div class="row">
    <!-- Primer info input -->
    <div class="col-3 d-flex align-items-center">
      <strong class="me-2">Docente: </strong>
      <input type="text" value="<?php echo $results['nombre'] . " " . $results['apellidoPaterno']; ?>" class="remove-styles-input form-control form-control-sm" disabled readonly>
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

<!-- LISTA DE ASISTECNIAS DEL ALUMNO -->
<!-- Botones desplegables Botones(Home,cursos,reportes) y Lista de los alumnos -->
<div class="container-fluid">
  <div class="row"> <!-- Dentro del row tenemos dos columnas -->
    
    <!-- Primer div (Home, cursos, reportes) -->
    <div class="col-1 space">
        <div class="row">
          <div class="col">
            <button type="button" class="btn btn-outline-success mb-2 small-column"><a href="usuario.php">Home</a></button>
            <button type="button" class="btn btn-outline-success mb-2 small-column"><a href="usuario.php">Cursos</a></button>
          </div>
        </div>
    </div>

    <!-- Segundo div (Lista de los alumnos)-->
    <div class="col-11 table-container space">
        <table class="table table-striped">
          
          <thead>
          
          <form action="#" method="post"> <!--- Inicio del form --->
            <tr> <!--- RECORRER MESES --->
              <th colspan="7" class="text-center" style="background-color: #FAF9F6;">
                <div class="d-flex justify-content-center align-items-center"> <!--- Inicio del mes actual y cambiarlo --->
                    <div class="form-months">
                      
                      <input type="hidden" name="mesActual" value="<?php echo $mesActual; ?>">
                      <input type="hidden" name="selected_day" value="<?php echo $selectedDay; ?>">
                                            
                      <button class="btn btn-primary btn-sm" name="decrementarMes"> <i class="fa-solid fa-angle-left"></i> </button>
                      <div > <?php echo $spanishMonth[$mesActual]; ?> </div>
                      <button class="btn btn-primary btn-sm" name="incrementarMes" id="next"> <i class="fa-solid fa-angle-right"></i> </button>
                                          
                    </div>
                  </div>
              </th>
            </tr>


            <tr class="sticky-column text-center">
              <th>Lunes</th>
              <th>Martes</th>
              <th>Miercoles</th>
              <th>Jueves</th>
              <th>Viernes</th>
              <th>Sabado</th>
            </tr>
          </thead>

          <tbody>

          <!-- Dias que asistio o falto el alumno -->
            <tr>
            <?php $diasSemana = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]; ?>
            <?php foreach ($diasSemana as $dia) { ?>
              <td class='text-center'>

                <?php
                  if(isset($asistenciaAlumno[$dia])){
                    foreach($asistenciaAlumno[$dia] as $fecha => $estadoAsistencia){
                      // echo $fecha . " " . $estadoAsistencia . "<br>";

                      $newFormatDate = date_create($fecha);
                      $nFecha = date_format($newFormatDate, 'd');

                      if($estadoAsistencia == 'asistio'){
                        // echo $fecha . " *" . "<br>";
                        echo $nFecha . " <i class='fa-solid fa-check'></i> " . $estadoAsistencia . "<br>";
                      } else {
                        // echo $fecha . " -" . "<br>";
                        echo $nFecha . " <i class='fa-solid fa-xmark'></i> " . $estadoAsistencia . "<br>";
                      }
                    }
                  }
                ?>

              </td>
            <?php } ?>
            </tr>

          </tbody>

          <tfoot>
          </tfoot>
          </form> <!--- Fin del form--->
        </table>
    </div>

  </div> 
</div>

</main> <!--- *** Fin del main *** --->

<footer class="bg-primary text-center text-while fixed-bottom">
  <div class="text-center text-white p-1">TSJZ - 2024 Copyright</div>
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