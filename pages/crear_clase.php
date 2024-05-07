<?php 
include_once "../db/conexion_pdo.php"; //Agregamos la conexion
require_once '../vendor/autoload.php';
session_start();

$materia = $_POST['asignatura'];
$asignatura = ucwords($materia); //Convierte a mayusculas el primer caracter de cada palabra de una cadena
$usuario = $_SESSION['id_usuario'];
//$archivo = $_POST['archivo']; //Variable con el nombre del archivo //ejemplo.xlsx
$semestre = $_POST['semestre'];
$fecha_inicio = $_POST['fechaInicio'];
$fecha_final = $_POST['fechaFinal'];

        if(!empty($materia) && !empty($materia) && !empty($_POST['dias'])){ //Si es diferente de vacio entra en la condicional
          $diasSeleccionados = $_POST['dias']; //Variable que guarda los dias de la semana (l,m,m,j,v,s)
          //Comprobar si existe el curso
          $stmt = $conn->prepare("SELECT * FROM materia WHERE idUsuario = :idusuario AND nombreMateria = :asignatura");
          $stmt->execute([':idusuario' => $usuario, ':asignatura' => $asignatura]);
          $row = $stmt->fetch(); //Obtengo lo valores por medio del fecth y el array lo guardo en la variable $row que contiene todos los datos del usuario consultado
    
        if($row > 0){
            $mensajes[] = "Ya existe el curso en tu catalogo";
        } else {
            //Crear materia
            $query_insert = "INSERT INTO materia (nombreMateria, idUsuario, semestre, fechaInicio, fechaFinal)
                      VALUES(:asignatura, :idusuario, :semestre, :fechaInicio, :fechaFinal)";
            $stmt_insert = $conn->prepare($query_insert);
            if($stmt_insert->execute([
                ':asignatura' => $asignatura,
                ':idusuario' => $usuario,
                ':semestre' => $semestre,
                ':fechaInicio' => $fecha_inicio,
                ':fechaFinal' => $fecha_final
            ])){

              $mensajes[] = "Materia creado exitosamente";
            
              //Insertar las fechas cuando estar disponible el curso
              $query_select = "SELECT idMateria, fechaInicio, fechaFinal FROM materia WHERE nombreMateria = :asignatura";
              $stmt_select = $conn->prepare($query_select);
              if($stmt_select->execute([':asignatura' => $asignatura])){
                  $rows = $stmt_select->fetch(PDO::FETCH_ASSOC);
            
                  $id_materia = $rows['idMateria'];
                  $fechaInicio = $rows['fechaInicio'];
                  $fechaFinal = $rows['fechaFinal'];

                  $fecha = array();
                  $fechaActual = new DateTime($fechaInicio);
                  $fechaFinal = new DateTime($fechaFinal);

                    while ($fechaActual <= $fechaFinal) { //Empieza con el inicio de la fecha actual y termina con fecha final
                    // $diaSemana = $fechaActual->format('N'); //1 (Lunes) a 7 (Domingo)
                    $diaSemana = $fechaActual->format('l'); //Obtener el nombre del dia de la semana
      
                    if (in_array($diaSemana, $diasSeleccionados)) {
                      $fechaActualFormato = $fechaActual->format('Y-m-d'); // Formato día-mes-año "format(d-m-Y)"
                      $fechas[] = array('fecha' => $fechaActualFormato, 'diaSemana' => $diaSemana); //Agregar la fecha y el dia al array de fechas disponibles
                    }
      
                    $fechaActual->modify('+1 day');
                    }

                      foreach ($fechas as $fecha){
                        $date = $fecha['fecha'];
                        $weekday = $fecha['diaSemana'];

                        //Verificamos si existe IdMateria y la fecha unica del dia de la semana que va estar disponible la materia 
                        $query = "SELECT COUNT(*) FROM materia_dia WHERE idMateria = :id_materia AND fecha = :formato_fecha";
                        $stmt_check = $conn->prepare($query);
                        if($stmt_check->execute([':id_materia' => $id_materia, ':formato_fecha' => $date])){
                          $rows = $stmt_check->fetchColumn();
                        }

                        if($rows > 0){
                          $mensaje[] = "Las fechas de la materia ya fueron registradas";
                        } else {
                            $query = "INSERT INTO materia_dia(idMateria, diaSemana, fecha)
                            VALUES (:id_materia, :dia_semana, :formato_fecha)";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([
                                ':id_materia' => $id_materia,
                                ':dia_semana' => $weekday,
                                ':formato_fecha' => $date
                            ]);

                        }
                            $mensajes[] = "Las fechas de la materia fueron registrados correctamente";
                      }

              }
            }

        }

        //Verificar si esta vacio el campo o sino se ha subido algun archivo excel
        } elseif(empty($_FILES["archivoExcel"]["name"])){ //Si entra en la condicion es porque tiene el excel cargado, sino la condicion se rompe
      
        // echo "<script> alert('Por favor, selecciona un archivo para subir'); window.location.href='docente.php'; </script>";
        $mensajes[] = "Por favor, selecciona un archivo excel para subir";

        } else {

        //Codigo para subir y alamacenar una archivo excel en nuestra carpeta de uploads del proyecto
        $archivo_nombre_original = basename($_FILES["archivoExcel"]["name"]); //El nombre original del el archivo para subirlo
        $archivo_tmp = $_FILES["archivoExcel"]["tmp_name"]; //El nombre temporal de el archivo en el cual el archivo sera subido para ser alamacenado en el server

        $archivo_extension = pathinfo($archivo_nombre_original, PATHINFO_EXTENSION); //Obtener la extension del archivo
        $nombre_orignal_sin_extension = pathinfo($archivo_nombre_original, PATHINFO_FILENAME); //Obtener el nombre sin la extension

        date_default_timezone_set("America/Mexico_City"); //Establecer la zona horaria del usuario
        //Generar un nuevo nombre con la fecha y hora actual, y extension
        $nuevo_archivo = $nombre_orignal_sin_extension . "_" . date("d-m-Y") . ".$archivo_extension";
        $nuevo_archivo_almacenado = basename($nombre_orignal_sin_extension . "_" . date("d-m-Y") . ".$archivo_extension");     
      
        //Directorio donde quieres guardar el archivo
        $directorio_destino = "/xampp/htdocs/sistema_de_monitoreo_estudiantil/uploads/";

        $archivo = $ruta_archivo = $directorio_destino . $nuevo_archivo_almacenado;

        //Comprueba si el archivo es excel o no (ya sea xlsx o xls)
        if($archivo_extension == "xlsx" || $archivo_extension == "xls"){

            if(move_uploaded_file($archivo_tmp, $directorio_destino . $nuevo_archivo)){ //El nombre del archivo temporal de el archivo en el cual el archivo subido fue almacenado en el server
              //echo "El archivo has sido subido correctamente";

              //$archivo = $ruta_archivo;
              $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

              $spreadsheet = $reader->load($archivo); //poner la ruta del archivo donde esta guardado
              $excelSheet = $spreadsheet->getActiveSheet();
              $spreadSheetArray = $excelSheet->toArray();
              $sheetCount = count($spreadSheetArray);
              
              //Ponemos un -1 en la variable $sheetCount-1, sino contara otra fila de mas que no contiene ningun valor y saltara error
              for($i=9; $i<$sheetCount; $i++){ //Conteo del array obtenido del Excel
                //Matricula Filas/Columnas
                //echo $spreadSheetArray[$i][1] . "<br>"; //imprimir el arrays y sus valores
                
                //La filas seran recorridas con el for mediante la variable $i, y el valor de las columnas queda estatica en la columna 0
                $matricula = $spreadSheetArray[$i][1]; //esta variable guarda el array quien contiene lo valores de cada celda del excel obtenido mendiate el recorrido con el for(){}
                $nombreCompleto = $spreadSheetArray[$i][2];

                //Limpiar lo datos para eliminar espacios adicionales y caracteres especiales
                $matricula_limpio = strtolower(trim($matricula));
                //Dominio de correo electronico deseado
                $dominio_correo = "@zapopan.tecmm.edu.mx";
                //Correo electronico completo
                $correo_completo = "za" . $matricula_limpio . $dominio_correo;

                if(!empty($matricula)){
                  $query_check = "SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula";
                  $stmt_check = $conn->prepare($query_check);
                  $stmt_check->execute([':matricula' => $matricula]);
                  $count = $stmt_check->fetchColumn();

                  if($count > 0){
                      $success = 1; //$mensajes[] = "La matricula ya existe en la base de datos";
                  } else {
                      $query_insert = "INSERT INTO usuarios (idRole, matricula, nombre, password, correo) VALUES (2, :matricula, :nombre, '12345678', :correo)";
                      $stmt_insert = $conn->prepare($query_insert);
                      if($stmt_insert->execute([':matricula' => $matricula, ':nombre' => $nombreCompleto, ':correo' => $correo_completo])){
                          $success = 2; //$mensajes[] = "Datos del excel insertados";
                      } else {
                        //Este es un mensaje si hubo un error de condificacion al momento de insertar en la db
                          $success = 3; //$mensajes[] = "Error en la insersion en la base datos";
                      }
                  }

                }

              } //Final del bucle for

              if($success == 1){
                $mensajes[] = "Las matriculas ya fueron registrados";
              } if($success == 2){
                $mensajes[] = "Datos del excel insertados";
              } if($success == 3){
                $mensajes[] = "Error en la insersion en la base de datos";
              }

                $columna = 'B';
                $filaInicial = 9; //Fila del excel donde queremos comenzar

                $totalFilas = $excelSheet->getHighestRow($columna);

                //Consultar el id de la materia
                $query_materia = "SELECT idMateria FROM materia WHERE nombreMateria = :asignatura";
                $stmt_materia = $conn->prepare($query_materia);
                $stmt_materia->execute([':asignatura' => $asignatura]);
                $rows = $stmt_materia->fetch(PDO::FETCH_ASSOC); //Obtenemos el id de la materia de la tabla materia
                $id_materia = $rows['idMateria'];

                for($fila = $filaInicial; $fila <= $totalFilas-1; $fila++){
                  $matricula = $spreadSheetArray[$fila][1];

                  //Consultar los idde los usuarios
                  $query_usuario = "SELECT idUsuario FROM usuarios WHERE matricula = :matricula";
                  $stmt_usuario = $conn->prepare($query_usuario);
                  $stmt_usuario->execute(['matricula' => $matricula]);
                  $rows = $stmt_usuario->fetch(PDO::FETCH_ASSOC); //Obtenemos los id de los usuarios de la tabla usuarios
                  // $id_usuario = $rows['idUsuario'];
                  if($rows !== false){ //Si es diferente de false, los valores son diferentes y no son del mismo tipo
                    
                    $id_usuario = $rows['idUsuario'];
                    // print_r($id_usuario);
                    $query_verify = "SELECT COUNT(*) FROM materia_alumno WHERE idUsuario = :idusuario AND idMateria = :idmateria";
                    $stmt_verify = $conn->prepare($query_verify);
                    $stmt_verify->execute([':idusuario' => $id_usuario, ':idmateria' => $id_materia]);
                    $count = $stmt_verify->fetchColumn();
  
                    if($count > 0){
                      echo "Las matriculas ya estan relacionadas<br>";
                    } else {
                      //Relacionar la materia con el alumno en la tabla materia_alumno
                      $query_insertion = "INSERT INTO materia_alumno (idUsuario, idMateria)
                                          VALUES (:idusuario, :idmateria)";
                      $stmt_insertion = $conn->prepare($query_insertion);
                      $stmt_insertion->execute([
                        ':idusuario' => $id_usuario,
                        ':idmateria' => $id_materia
                      ]);

                    }

                      echo "El alumno y materia se relaciono correctamente<br>";
                    
                  }




              }
              
                  
            } else {
                $mensajes[] = "Error al subir el archivo";
            }

      } else {
          $mensajes[] = "Lo siento, solo se permiten archivos Excel";
      }
    
    }

    // //alert box
    // if(isset($mensajes)){
    //   //Enlaza los estilos de Bootstrap
    //   $Msg = "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
    //   $Msg .= "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js'></script>";

    //   $Msg .= "<div class='alert alert-warning text-center' role='alert'>";
    //   foreach ($mensajes as $mensaje){
    //     //echo "<script> alert('$mensaje'); window.location.href='docente.php' </script>";
    //     $Msg .= '<strong>' . $mensaje . '</strong><br>';
    //   }

    //   $Msg .= '</div>';
      
    //   echo $Msg;

    //   echo "<script>setTimeout(function(){ window.location.href = 'docente.php'; }, 2000);</script>";

    // }

    //$_SESSION['mensajes'] = $mensajes; //Almacena el array de mensajes en una variable de sesion
    //header("Location: docente.php"); //Redirige a la pagina
    //exit(); //Asegura que el script se detenga despues de redirigir

?>


