<?php 
include_once "../db/conexion_pdo.php"; //Agregamos la conexion
require_once '../vendor/autoload.php';
session_start();
//print_r($_SESSION);
//echo "<br>";
if(!empty($_POST['asignatura'] && !empty($_POST['semestre']))){ //Si es diferente de vacio entra en la condicional
    // var_dump($_POST['asignatura']);
    // print_r($_POST['submit']);
    $materia = $_POST['asignatura'];
    $asignatura = ucwords($materia); //Convierte a mayusculas el primer caracter de cada palabra de una cadena
    //print_r($_SESSION['usuario']);
    $usuario = $_SESSION['usuario'];
    //$archivo = $_POST['archivo']; //Variable con el nombre del archivo //ejemplo.xlsx
    // otra rutina para obtener los datos d elso alumnos del archivo
    // realiazo los insert de cada usuario para matricularlo en dicha materia
    // $matricula = $_POST['matricula'];
    $semestre = $_POST['semestre'];
    $fecha_inicio = $_POST['fechaInicio'];
    $fecha_final = $_POST['fechaFinal'];

        //Comprobar si existe el curso
        $stmt = $conn->prepare("SELECT * FROM materia WHERE idUsuario = :idusuario AND nombreMateria = :asignatura");
        $stmt->execute([':idusuario' => $usuario, ':asignatura' => $asignatura]);
        $row = $stmt->fetch(); //Obtengo lo valores por medio del fecth y el array lo guardo en la variable $row que contiene todos los datos del usuario consultado
    
        if($row > 0){

            echo "<script> alert('Ya existe el curso en tu catalogo'); window.location.href='docente.php'; </script>";

        } else {
            
            //Crear curso
            $query = "INSERT INTO materia (nombreMateria, idUsuario, semestre, fechaInicio, fechaFinal)
                    VALUES(:asignatura, :idusuario, :semestre, :fechaInicio, :fechaFinal)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':asignatura' => $asignatura,
                ':idusuario' => $usuario,
                'semestre' => $semestre,
                'fechaInicio' => $fecha_inicio,
                'fechaFinal' => $fecha_final
            ]);

            echo "<script> alert('Creado existosamente'); window.location.href='docente.php'; </script>";
        }
      //Verifica si esta vacio
      //Verificar si se ha subido algun archivo  
    } elseif(empty($_FILES["archivoExcel"]["name"])){ //Si entra en la condicion es porque tiene el excel cargado, sino la condicion se rompe
      
      // echo "Por favor, selecciona un archivo para subir";
      echo "<script> alert('Por favor, selecciona un archivo para subir'); window.location.href='docente.php'; </script>";

    } else {

      //Codigo para subir y alamacenar una archivo excel en nuestra carpeta de uploads del proyecto 
      $directorio_destino = "/xampp/htdocs/sistema_de_monitoreo_estudiantil/uploads/"; //directorio donde se subiran los archivos
      $nombre_archivo = basename($_FILES["archivoExcel"]["name"]); //El nombre original del el archivo para se subido
      $ruta_archivo = $directorio_destino . $nombre_archivo; //Destino de la ruta y archivo
      //$uploadSuccess = 1;
      $FileType = pathinfo($ruta_archivo, PATHINFO_EXTENSION); //Retorna la extension del archivo
      //Comprueba si el archivo es excel o no (ya sea xlsx o xls)
      if($FileType == "xlsx" || $FileType == "xls"){
        if(file_exists(false)){ //Verifica si ya existe en la ruta especificada //if(file_exists($ruta_archivo))
              echo "Lo siento, el archivo ya existe";
        }else {
            if(move_uploaded_file($_FILES["archivoExcel"]["tmp_name"], $ruta_archivo)){ //El nombre del archivo temporal de el archivo en el cual el archivo subido fue almacenado en el server
              //echo "El archivo has sido subido correctamente";
              $archivo = $ruta_archivo;
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
                
                //Nombre del alumno
                echo $spreadSheetArray[$i][2] . "<br>";
                
                if(!empty($matricula)){
                  $query_check = "SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula";
                  $stmt_check = $conn->prepare($query_check);
                  $stmt_check->execute([':matricula' => $matricula]);
                  $count = $stmt_check->fetchColumn();

                  if($count > 0){
                    $success = false; //echo "La matricula ya existe en la base de datos";
                  } else{
                    $query_insert = "INSERT INTO usuarios (matricula) VALUES (:matricula)";
                    $stmt_insert = $conn->prepare($query_insert);
                      if($stmt_insert->execute([':matricula' => $matricula])){
                        $success = true; //echo "Datos del excel insertado"
                      } else {
                        //Este es un mensaje si hubo un error de condificacion al momento de insertar en la db
                        echo "Error en la insersion en la base datos";
                      }
                  }

                }

              }

                /*Aqui verificamos si existen los usuarios sera un booleano(false), 
                si no existe el usuario sera un booleano(true) e insertara el nuevo usuario en la tabla usuarios */
                if($success == true){
                  //var_dump($success);
                  echo "Exito! Datos importados insertados correctamente";
                  // echo "Exito! Datos importados insertados correctamente" . " " . $matricula;
                } else { //elseif($success == false)
                  //var_dump($success);
                  echo "Los usuarios ya existen en la base de datos";
                }
 
            } else {
              echo "<script> alert('Error al subir el archivo'); window.location.href='docente.php'; </script>";
            }
        }

      } else {
          echo "<script> alert('Lo siento, solo se permiten archivos Excel'); window.location.href='docente.php'; </script>";
      }
    
    } 


?>