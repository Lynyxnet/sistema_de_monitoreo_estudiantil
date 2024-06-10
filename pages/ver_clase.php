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

//MOSTRAR LOS ALUMNOS DE LA MATERIA CON EL ID QUE OBTENEMOS POR $_GET
$query_datos = "SELECT 
                  usuarios.matricula, 
                  usuarios.nombre, 
                  usuarios.apellidoPaterno, 
                  usuarios.apellidoMaterno, 
                  usuarios.correo
                FROM materia_alumno
                INNER JOIN usuarios ON materia_alumno.idUsuario = usuarios.idUsuario
                WHERE materia_alumno.idMateria = :page_id;
                ";
  $stmt_datos = $conn->prepare($query_datos);
  $stmt_datos->execute([':page_id' => $page_id]);
  $results = $stmt_datos->fetchAll();
  $numerosFilas = count($results);
  // print_r($results);

$contador = 1; //Inicializamos el contador
$count = 1; //Incializamos el contador

//CONSULTAR diaSemana, fecha DE LA MATERIA CON BASE EN SU ID
$query_audit = "SELECT diaSemana, fecha 
                FROM materia_dia 
                WHERE idMateria = $page_id";
  $stmt_audit = $conn->prepare($query_audit);
  $stmt_audit->execute();
  $rows = $stmt_audit->fetchAll();

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

  // INFO DE FECHAS PARA MOSTRAR ANIO INICIO Y FINAL DE LA MATERIA
  $mes_inicio = $meses['mesInicio'];
  $mes_final = $meses['mesFinal'];
  $year = $meses['year'];

// FIN DE LA CONSULTA DE MES INICIO Y FINAL
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
  //$mesActual = date('n');
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

// echo $mesActual;
// echo $selectedDay;

// Dia de la semana que esta siendo seleccionado
// $WeekDaysSelected = array(1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles' , 4 => 'Jueves', 5 => 'Viernes' , 6 => 'Sabado');
// $WeekDaysSelected[$selectedDay];

$nDaysArray = [];
$fechasArray = [];

//GUARDAR E IMPORTAR LOS DIAS Y FECHAS DEL MES REGISTRADO
foreach($rows as $fila){
  $fecha = new DateTime($fila['fecha']);
  if($fecha->format('n') == $mesActual){
      if($fecha->format('N') == $selectedDay){
      // echo "Mes: $mesActual" . " " . $fila['diaSemana'] . " " . $fila['fecha'] . "<br>";
      // $dias_semana[$fila['diaSemana']][] = $fila['fecha'];

      $date = new DateTime($fila['fecha']);
      $nDays = $fecha->format('j'); //Convertimos la fecha en un numero del dia del mes
      $nDaysFormat = $fecha->format('Y-m-d'); //Convertimos el dia a fecha (year-month-day)
      // echo $nDays;

      // Vamos ingresar a cada fecha del array $fechasArray[] con los $key mediante foreach
      $fechasArray[] = $fecha->format('Y-m-d'); //Fecha con formato Y-m-d
      $nDaysArray[] = $nDays; //Fechas con formato numero del dia del mes      
      }
  }
}

//INSERTAR ASISTENCIAS DE LOS ALUMNOS CON LA FECHA SELECCIONADA
if(!empty($_POST['enviar'])){

  //Verificamos que boton envio la peticion "submit1"
  if($_POST['enviar'] == 'submit1'){
    //VERIFICAR matrciulas[], asistencias[], fecha_seleccionada
    if(!empty($_POST['matriculas']) && !empty($_POST['asistencias']) && !empty($_POST['fecha_seleccionada'])){
      $matriculas = $_POST['matriculas'];
      $asistencias = $_POST['asistencias'];
      $fecha_seleccionada = $_POST['fecha_seleccionada'];
   
      foreach($asistencias as $key => $asistencia) {
        // Convertimos la matricula en string
        $str_matricula = (string)$key;
  
        // Obtenemos el idUsuario basado en la matrícula
        $query_examine = "SELECT idUsuario FROM usuarios WHERE matricula = :matricula";
        $stmt_examine = $conn->prepare($query_examine);
        $stmt_examine->execute([':matricula' => $str_matricula]);
        $rows = $stmt_examine->fetchAll(PDO::FETCH_ASSOC);
  
        foreach($rows as $row) {
            $id_usuario = $row['idUsuario'];
  
            // Obtener el idMateriaDia basado en la materia y fecha seleccionada
            $query_values = "SELECT idMateriaDia, diaSemana, fecha
                            FROM materia_dia
                            WHERE idMateria = :materia AND fecha = :fecha";
            $stmt_values = $conn->prepare($query_values);
            $stmt_values->execute([':materia' => $page_id, 'fecha' => $fecha_seleccionada]);
            $row = $stmt_values->fetch(PDO::FETCH_ASSOC);
            $id_materia_dia = $row['idMateriaDia'];
  
            // Verificamos si ya existe un registro de asistencia para el usuario y el día específico
            $query_check = "SELECT * FROM asistencias
                            WHERE idUsuario = :id_usuario 
                            AND idMateriaDia = :id_materia_dia";
            $stmt_check = $conn->prepare($query_check);
            $stmt_check->execute([
                ':id_usuario' => $id_usuario, 
                ':id_materia_dia' => $id_materia_dia,
            ]);
            $exist = $stmt_check->fetch(PDO::FETCH_ASSOC);
  
            if ($exist) {
                // Si ya existe un registro de asistencia, no hacemos nada
                $success = 2;
            } else {
                // Insertar la nueva asistencia
                $query_add_data = "INSERT INTO asistencias (idUsuario, idMateriaDia, asistencia)
                                  VALUES (:id_usuario, :id_materia_dia, :asistencia)";
                $stmt_add_data = $conn->prepare($query_add_data);
                $stmt_add_data->execute([
                    ':id_usuario' => $id_usuario,
                    ':id_materia_dia' => $id_materia_dia,
                    ':asistencia' => ($asistencia == "asistio") ? 1 : 2
                ]);
  
                $success = 1;
            }
        }
      }  

      if($success == 1){
      $mensajes[] = "Registro existoso";
      } elseif($success == 2){
      $mensajes[] = "Los alumnos ya estan registrados en esta fecha!!!";
      }

  }

  } elseif($_POST['enviar'] == 'submit2'){
    
    if(!empty($_POST['matriculas']) && !empty($_POST['editar_faltas']) && !empty($_POST['fecha_seleccionada'])){
      $MatriculasPorFaltas = $_POST['editar_faltas'];
      $fecha_seleccionada = $_POST['fecha_seleccionada'];

      foreach($MatriculasPorFaltas as $ControlEscolar => $value){
        $MatriculasF = $ControlEscolar;
        // echo $MatriculasF;
      
        $query_findings = "SELECT 
                          usuarios.matricula,
                          usuarios.idUsuario,
                          materia_dia.idMateria,
                          materia_dia.fecha,
                          asistencias.asistencia
                         FROM asistencias
                         JOIN usuarios ON asistencias.idUsuario = usuarios.idUsuario
                         JOIN materia_dia ON asistencias.idMateriaDia = materia_dia.idMateriaDia
                         WHERE usuarios.matricula = :matricula AND materia_dia.idMateria = :materia AND materia_dia.fecha = :fecha";
        $stmt_findings = $conn->prepare($query_findings);
        $stmt_findings->execute([':matricula' => $MatriculasF, 'materia' => $page_id, ':fecha' => $fecha_seleccionada]);
        $Absences = $stmt_findings->fetchAll(PDO::FETCH_ASSOC);
        // echo var_dump($Absences) . "<br>";

        if($Absences > 0){ //Comprobar si hay rows(resultados)
          foreach($Absences as $Absence){ //Iteramos los datos de la variable $Absences, y accedemos a los valores mediante su nombre de la columna
            $asistencia = $Absence['asistencia'];
            $matricula = $Absence['matricula'];
            $idUsuarioUpdate = $Absence['idUsuario'];

            if($asistencia == "asistio"){
              $success = 3;
              // echo "Asistio: " . $id_usuario . " " . $matricula . "<br>";
            } elseif($asistencia == "falto") {
              // echo "No asistio " . $matricula . "<br>";

              $query_update = "UPDATE
                                  asistencias
                               SET 
                                  asistencia = :asistencia
                               WHERE 
                                  idUsuario = :id_usuario";
              $stmt_update = $conn->prepare($query_update);
              $stmt_update->execute(['asistencia' => 'asistio', 'id_usuario' => $idUsuarioUpdate]);

              $success = 4;
            }
            
          }
        }

      }

      if($success == 3){
        $mensajes[] = "Ya tiene asistencia. Escoge otro diferente";
        } elseif($success == 4){
        $mensajes[] = "La falta fue actualizada";
      }
      
    } else {
      $mensajes[] = "No hay datos enviados para actualizar.";
    }
    
  }

}

// CONSULTAR LA TABLA "ASISTENCIAS"
$query_asistencia = "SELECT
                        usuarios.matricula,
                        asistencias.asistencia,
                        materia_dia.diaSemana,
                        materia_dia.fecha
                     FROM asistencias
                     JOIN materia_dia ON asistencias.idMateriaDia = materia_dia.idMateriaDia
                     JOIN usuarios ON asistencias.idUsuario = usuarios.idUsuario
                     WHERE materia_dia.idMateria = :id_materia";
  $stmt_asistencia = $conn->prepare($query_asistencia);
  $stmt_asistencia->execute([':id_materia' => $page_id]);
  $asistencias = $stmt_asistencia->fetchAll(PDO::FETCH_ASSOC);
  // echo var_dump($asistencias);

// VERIFICAR SI LA TABLA ASISTENCIAS TIENE ALUMNO Y SUS ASISTENCIAS

if(!empty($asistencias)){

  //Guardar el un array asociativo la matricula y fecha
  foreach ($asistencias as $asistencia) {
  $asistenciaPorMatricula[$asistencia['matricula']][$asistencia['fecha']] = $asistencia['asistencia'];
  }

  // Calcular total de asistencias y faltas por matrícula
  $totalesPorMatricula = [];
  foreach ($asistenciaPorMatricula as $matricula => $asistencias) { 
    $totalAsistencias = 0;
    $totalFaltas = 0;
    foreach ($asistencias as $asistencia) {
        if ($asistencia == 'asistio') {
            $totalAsistencias++;
        } else {
            $totalFaltas++;
        }
    }
    //En el array "$totalesPorMatricula" en el indice por matricula se guardan el total de asistio y falto
    $totalesPorMatricula[$matricula] = [
        'asistencias' => $totalAsistencias,
        'faltas' => $totalFaltas
    ];
  }

} else {

  $msgs[] = "No hay alumno o ni asistencia";

}

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

//TRADUCIR LOS MESES AL ESPANOL
$spanishMonth[$mesActual];

//Alert box - Mensajes en pantalla de termino de una tarea realizada
if(isset($mensajes)){
    //Recorre los mensajes y muestra un alert box de bootstrap para cada uno
    foreach($mensajes as $mensaje){
      echo "<div id='alertBox' class='alert alert-warning small text-center' role='alert'>";
      echo "<strong>" . $mensaje . "</strong>";
      echo "</div>";
    }
    unset($mensajes);
    echo "<script> setTimeout( function(){window.location.href='ver_clase.php?id=$page_id'}, 3000); </script>";
} elseif(isset($msgs)){
    foreach($msgs as $msg){
      echo "<div id='alertBox' class='alert alert-warning small text-center' role='alert'>";
      echo "<strong>" . $msg . "</strong>";
      echo "</div>";
    }
    unset($msgs);
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
    <script src="../js/OcultarAlertBox.js"></script>
    <script src="../js/confirmarActualizar.js"></script>
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
      <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#editarAlumnos">Editar alumnos</button>
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

<!-- LISTA ALUMNOS -->
<!-- Botones desplegables Botones(Home,cursos,reportes) y Lista de los alumnos -->
<div class="container-fluid">
  <div class="row"> <!-- Dentro del row tenemos dos columnas -->
    
    <!-- Primer div (Home, cursos, reportes) -->
    <div class="col-1 space">
        <div class="row">
          <div class="col">
            <button type="button" class="btn btn-outline-success mb-2 small-column"><a href="docente.php">Home</a></button>
            <button type="button" class="btn btn-outline-success mb-2 small-column"><a href="docente.php">Cursos</a></button>
            <button type="button" class="btn btn-outline-success mb-2 small-column">Reportes</button>
          </div>
        </div>
    </div>

    <!-- Guardamos los dias que contiene el mes seleccionado, $numero servira para el tamano "colspan" del th -->
    <?php $numeros = $nDaysArray ?>

    <!-- Segundo div (Lista de los alumnos)-->
    <div class="col-11 table-container space">
        <table class="table table-striped">
          
          <thead>
          <form action="#" method="post"> <!--- Inicio del form --->
            <tr> <!--- RECORRER MESES --->
              <th colspan="<?php echo (count($numeros)+7); ?>" class="text-center" style="background-color: #FAF9F6;">
                <div class="d-flex justify-content-center align-items-center"> <!--- Inicio del mes actual y cambiarlo --->
                    <div class="form-months">
                      
                      <input type="hidden" name="mesActual" value="<?php echo $mesActual; ?>">
                      <input type="hidden" name="selected_day" value="<?php echo $selectedDay; ?>">
                      
                      <button class="btn btn-outline-dark btn-sm" name="prev_day" id="prev"> <i class="fa-solid fa-angle-left"></i> </button>
                      
                      <button class="btn btn-primary btn-sm" name="decrementarMes"> <i class="fa-solid fa-angle-left"></i> </button>
                      <div> <?php echo $spanishMonth[$mesActual]; ?> </div>
                      <button class="btn btn-primary btn-sm" name="incrementarMes" id="next"> <i class="fa-solid fa-angle-right"></i> </button>
                      
                      <button class="btn btn-outline-dark btn-sm" name="next_day"> <i class="fa-solid fa-angle-right"></i> </button>
                    
                    </div>
                  </div>
              </th>
            </tr>


            <tr class="sticky-column text-center">
              <th>#</th>
              <th>Matricula</th>
              <th>Nombre completo</th>

              <!--- Columnas que despliegan los dias del mes seleccionado --->
              <?php foreach($nDaysArray as $dias) { ?>
                <th class='text-center'> <?php echo $dias; ?> </th>
              <?php } ?>

              <!--- Columna donde se elige el dia y se seleciona (radio button) al alumno para su asistencia --->
              <th style="width: 130px;"> 
              <select name="fecha_seleccionada"> 
                <?php foreach($nDaysArray as $key => $dias) echo "<option value='$fechasArray[$key]'> $dias </option>"; ?> 
              </select> Asistio
              </th>

              <th class="text-center"><i class="fa-solid fa-user-pen"></i></th>

              <th>T.Asistio</th>
              <th>T.Falto</th>
            </tr>

          </thead>
          <tbody>
          
            <?php foreach ($results as $result): ?> <!--- inicio del foreach --->
            <tr> <!--- Inicio de tr (fila) --->
            
              <td class="text-center"> <?php echo $contador; ?> </td> <!--- Mostramos el contador --->
            
              <td class="text-center" style="width: 25px;">
                <?php echo $result['matricula']; ?> <!--- Muestra la matricula del alumno --->
                <!-- Seleccionada la matricula se guarda -->
                <input type='hidden' name='matriculas[]' value='<?php echo $result['matricula']; ?>' > <!--- Guarda las matriculas seleccionadas --->
              </td>

              <!--- Muestra el nombre del alumno --->
              <td> 
                <?php 
                  echo $result['nombre'] . " " . $result['apellidoPaterno'] . " " . $result['apellidoMaterno'];
                ?> 
              </td>

              <!-- Dias que asistio o falto el alumno -->
              <?php foreach ($fechasArray as $fecha) { ?>
                <td class='text-center'>
                  <?php
                  
                    if (isset($asistenciaPorMatricula[$result['matricula']][$fecha])) {
                      $estatus  = $asistenciaPorMatricula[$result['matricula']][$fecha]; // Mostrar estado de asistencia
                        if($estatus == 'asistio'){
                          echo "<strong>*<strong>";
                        } else {
                        echo "-";// Valor para indicar que no asistió
                        }
                    }

                  ?>
                </td>
              <?php } ?>

              <!-- Tomar asistencia del alumno -->
              <td class="text-center">
                <!-- Opcion de ASISTIO -->
                <input type='radio' name='asistencias[<?php echo $result['matricula']; ?>]' id="choose_yes" value='asistio'>Asistio
                <!-- FALTO -->
                <!-- Ponemos "Checked" la asistencia del alumno, si el profesor lo cambia, ya no tendra este valor  -->
                <input type='radio' name='asistencias[<?php echo $result['matricula']; ?>]' value='falto' id="input-falto" checked>
              </td>
              
              <!-- Opcion para cambiar la falta del alumno que llego por una asistencia -->
              <td class="text-center">
                <!-- Es un input checkbox para seleccionar el/los usuario(s) que van a cambiar su falta -->
                 <!-- Enviamos los valores de la matricula mediante name="editar_faltas", de las matriculas que van a ser cambiados -->
                <input type='checkbox' name='editar_faltas[<?php echo $result['matricula']; ?>]' value='CambiarFalta'>
              </td>

              <td class='text-center'>
                <?php
                // Mostrar el total de asistencias
                if (isset($totalesPorMatricula[$result['matricula']])) {
                    echo $totalesPorMatricula[$result['matricula']]['asistencias'];
                } else {
                    echo '0';
                }
                ?>
              </td>

              <td class='text-center'>
                <?php
                // Mostrar el total de faltas
                if (isset($totalesPorMatricula[$result['matricula']])) {
                    echo $totalesPorMatricula[$result['matricula']]['faltas'];
                } else {
                    echo '0';
                }
                ?>
              </td>
          
            <?php $contador++; ?> <!--- //Incrementamos el contador para siguiente fila --->
            <?php endforeach; ?> <!--- final del foreach --->
            </tr>

          </tbody>

          <tfoot>
            <tr>
              <td colspan="<?php echo (count($numeros)+7); ?>" style="background-color: #FAF9F6;">
                <div class="d-flex justify-content-end">
                  <input type="button" class="btn btn-outline-dark" id="clear_radio" style="margin-right: 5px;" value="Borrar">
                  <button type="submit" class="btn btn-outline-dark" style="margin-right: 5px;" name="enviar" value="submit2">Actualizar</button>
                  <button type="submit" class="btn btn-success" name="enviar" value="submit1">Registrar</button>
                </div>
              </td>
            </tr>
          </tfoot>
          </form> <!--- Fin del form--->
        </table>
    </div>

  </div> 
</div>

<!-- Modal - Donde vemos las opciones para eliminar/editar alumnos -->
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

                  <td class="text-center"> 
                    <?php echo $result['matricula'] ?>
                    <input type='hidden' name='matriculas[]' value='<?php echo $result['matricula']; ?>' >
                  </td>

                  <td> <?php echo $result['nombre']; ?> </td>
                  <td> <?php echo $result['apellidoPaterno']; ?> </td> <!--- Apellido paterno --->
                  <td> <?php echo $result['apellidoMaterno']; ?> </td> <!--- Apellido materno --->
                  <td> <?php echo $result['correo']?> </td>
                  
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
                <?php } ?> <!--- Termino del foreach --->
            </tbody>
          </table>
          </div>
 
            <!-- Modal footer -->
            <div class="modal-footer">
              <!-- <button type="button" onclick="limpiarDatos()" class="btn btn-primary">Borrar todos</button> -->
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

            </div>

          </form> <!--- Fin del form ---->

        </div>
    </div>
</div>

<!-- Sub-modal - Editar los datos del alumno -->

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
      </form>

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