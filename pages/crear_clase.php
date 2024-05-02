<?php 
include_once "../db/conexion_pdo.php"; //Agregamos la conexion
require_once '../vendor/autoload.php';
session_start();

$materia = $_POST['asignatura'];
$asignatura = ucwords($materia);       //Convierte a mayusculas el primer caracter de cada palabra de una cadena
$usuario = $_SESSION['nombre'];       //Obtenemos el "nombre" del maestro
$id_usuario = $_SESSION['id_usuario']; //Obetenmos el "id" del maestro
//$archivo = $_POST['archivo'];        //Variable con el nombre del archivo //ejemplo.xlsx
$semestre = $_POST['semestre'];
$fecha_inicio = $_POST['fechaInicio'];
$fecha_final = $_POST['fechaFinal'];

        if(!empty($_POST['asignatura']) && !empty($_POST['semestre'])) { //Si es diferente de vacio entra en la condicional

        //Comprobar si existe el curso
        $stmt = $conn->prepare("SELECT * FROM materia WHERE idUsuario = :id_usuario AND nombreMateria = :asignatura");
        $stmt->execute([':id_usuario' => $id_usuario, ':asignatura' => $asignatura]);
        $row = $stmt->fetch(); //Obtengo lo valores por medio del fecth y el array lo guardo en la variable $row que contiene todos los datos del usuario consultado
    
        if($row > 0){

            echo "<script> alert('Ya existe el curso en tu catalogo'); window.location.href='docente.php'; </script>";

        } else {
            
            //Crear curso
            //Nombre orignales de la tabla escrito corrcetamente porque mysql es sensible minucuslas y mayusculas
            $query = "INSERT INTO materia (nombreMateria, idUsuario, semestre, fechaInicio, fechaFinal) 
                    VALUES(:asignatura, :id_usuario, :semestre, :fechaInicio, :fechaFinal)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':asignatura' => $asignatura,
                ':id_usuario' => $id_usuario,
                ':semestre' => $semestre,
                ':fechaInicio' => $fecha_inicio,
                ':fechaFinal' => $fecha_final
            ]);

            echo "<script> alert('Creado existosamente'); window.location.href='docente.php'; </script>";
        }
        }

        $last_id = $conn->lastInsertId();

        //ARCHIVO EXCEL
        //Verificar si esta vacio o sino se ha subido algun archivo  
        if(empty($_FILES["archivoExcel"]["name"])) { //Si entra en la condicion es porque tiene el excel cargado, sino la condicion se rompe
      
          echo "<script> alert('Por favor, selecciona un archivo para subir'); window.location.href='docente.php'; </script>";

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

              $asignaturaExcel = $spreadSheetArray[4][2]; //Nombre de la asignatura obtenida desde el excel

              //CREAR ASIGNATURA
              if(!empty($asignaturaExcel)){
                //Checa si ya existen las matriculas dentro de la tabla "materia"
                $query_check = "SELECT COUNT(*) FROM materia WHERE nombreMateria = :asignatura";
                $stmt_check = $conn->prepare($query_check);
                $stmt_check->execute(['asignatura' => $asignaturaExcel]);
                $count = $stmt_check->fetchColumn();

                if($count > 0){
                  // echo "<script> alert('La asignatura ya fue creada'); window.location.href='docente.php'; </script>";
                } else { 
                  //INSERTAR MATERIA
                  //idUsuario es el id del maestro
                  //falta fechaInicio y fechaFinal
                  $query_insert = "INSERT INTO materia (nombreMateria, idUsuario, semestre) VALUES(:asignatura, :id_usuario, :semestre)";
                  $stmt_insert = $conn->prepare($query_insert);
                  if($stmt_insert->execute([':asignatura' => $asignaturaExcel, ':id_usuario' => $id_usuario, 'semestre' => $semestre])){
                    echo "<script> alert('Exito! Asignatura creada'); window.location.href='docente.php'; </script>";
                  }
                }

              //CREAR MATRICULA
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
                    $success = false; //echo "La matricula ya existe en la base de datos";
                  } else {
                    //INSERTAR ALUMNOS
                    $query_insert = "INSERT INTO usuarios (idRole, matricula, nombre, password, correo) VALUES (2, :matricula, :nombre, '12345678', :correo)";
                    $stmt_insert = $conn->prepare($query_insert);
                    if($stmt_insert->execute([':matricula' => $matricula, ':nombre' => $nombreCompleto, ':correo' => $correo_completo])){

                        $success = true; //echo "Datos del excel insertado"

                    } else {
                        //Este es un mensaje si hubo un error de condificacion al momento de insertar en la db
                        echo "Error en la insersion en la base datos";
                    }
                  }

                }

                }

                //RELACIONAR EL ALUMNO CON LA MATERIA YA REGISTRADA ANTERIORMENTE CON EL ARCHIVO EXCEL
                $columna = 'B'; //Columna espefica
                $filaInicial = 9; //Fila inicial en la cual se comienza contar

                $totalFilas = $excelSheet->getHighestRow($columna); //Obtener el numero total de filas con datos en la columna
                
                for($fila = $filaInicial; $fila<$totalFilas; $fila++){

                $asignaturaDelArchivo = $spreadSheetArray[4][2];    //Ubicacion del nombre de la materia
                $matriculaDelArchivo = $spreadSheetArray[$fila][1]; //Ubicacion de las matriculas de la materia

                if(!empty($asignaturaDelArchivo) && !empty($matriculaDelArchivo)){ //Comprueba si la celda en cada iteracion tiene datos, sino tiene datos se rompe la condicion
                
                //CONSULTAR MATERIA
                $query_select_asignatura = "SELECT idMateria FROM materia WHERE nombreMateria = :nombre_materia";
                $stmt_select_asignatura = $conn->prepare($query_select_asignatura);
                $stmt_select_asignatura->execute([':nombre_materia' => $asignaturaDelArchivo]);
                $result = $stmt_select_asignatura->fetch(PDO::FETCH_ASSOC);
                $idmateria = $result['idMateria'];
                
                //CONSULTAR USUARIO
                $query_select = "SELECT idUsuario FROM usuarios WHERE idRole = 2 AND matricula = :matricula";
                $stmt_select = $conn->prepare($query_select);
                $stmt_select->execute([':matricula' => $matriculaDelArchivo]);
                $rows = $stmt_select->fetch(PDO::FETCH_ASSOC);
                $iduser = $rows['idUsuario'];
                  
                  //CONSULTAR USUARIO Y MATERIA SI EXISTE EN LA TABLA DONDE SE RELACIONA
                  $query_check = "SELECT COUNT(*) FROM materia_alumno WHERE idUsuario = :idusuario AND idMateria = :idmateria";
                  $stmt_check = $conn->prepare($query_check);
                  $stmt_check->execute([':idusuario' => $iduser, ':idmateria' => $idmateria]);
                  $usuariosContados = $stmt_check->fetchColumn();

                  if($usuariosContados > 0){
                    
                    // echo "<script> alert('Alumnos ya estan en la materia'); window.location.href='docente.php'; </script>";
                    echo "El alumno ya esta relacionado con la materia" . "<br>";

                  } else {
                    
                    $query_insert = "INSERT INTO materia_alumno (idUsuario, idMateria) VALUES (:idusuario, :idmateria)";
                    $stmt_insert = $conn->prepare($query_insert);
                    $stmt_insert->execute([':idusuario' => $iduser, ':idmateria' => $idmateria]);

                    echo "Exito! Usuario relacionado con la materia" . "<br>";
                    
                  }

                  }

                }

              }
              

              }

                  /*Aqui verificamos si existen los usuarios sera un booleano(false), 
                  si no existe el usuario sera un booleano(true) e insertara el nuevo usuario en la tabla usuarios */
                  if($success == true){
                    // echo "Exito! Datos importados insertados correctamente";
                    //echo "<script> alert('Exito! Datos importados insertados correctamente'); window.location.href='docente.php'; </script>";
                    // echo "Exito! Datos importados insertados correctamente" . " " . $matricula;
                  }   else {
                    //echo "Los usuarios ya existen en la base de datos";
                    // echo "<script> alert('Los usuarios ya existen en la base de datos'); window.location.href='docente.php'; </script>";
                  }
 
            } else {
              echo "<script> alert('Error al subir el archivo'); window.location.href='docente.php'; </script>";
            }


          

    
    }

?>