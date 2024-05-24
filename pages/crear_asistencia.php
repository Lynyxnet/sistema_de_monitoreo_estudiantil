<?php
// Consultar la información de las clases programadas según la tabla MateriaDias
$sql_clases = "SELECT idMateriaDia, idMateriaAlumno, fecha
               FROM MateriaDias";

$result_clases = $conn->query($sql_clases);

if ($result_clases->num_rows > 0) {
    // Iterar sobre cada clase programada
    while ($row_clase = $result_clases->fetch_assoc()) {
        $idMateriaDia = $row_clase["idMateriaDia"];
        $fecha_clase = $row_clase["fecha"];

        // Consultar los alumnos inscritos en esta materia para este día de clase
        $sql_alumnos = "SELECT idMateriaAlumno
                        FROM MateriaAlumno
                        WHERE idMateriaAlumno IN (
                            SELECT idMateriaAlumno
                            FROM MateriaDias
                            WHERE fecha = '$fecha_clase'
                        )";

        $result_alumnos = $conn->query($sql_alumnos);

        if ($result_alumnos->num_rows > 0) {
            // Generar registros de asistencia inicialmente marcados como ausentes ('falta')
            while ($row_alumno = $result_alumnos->fetch_assoc()) {
                $idMateriaAlumno = $row_alumno["idMateriaAlumno"];
                
                // Insertar registro de asistencia
                $sql_insert_asistencia = "INSERT INTO Asistencia (idMateriaAlumno, idMateriaDia, asistencia)
                                          VALUES ($idMateriaAlumno, $idMateriaDia, 'falta')";
                
                if ($conn->query($sql_insert_asistencia) === TRUE) {
                    echo "Asistencia registrada para el alumno con ID: $idMateriaAlumno para la clase del día $fecha_clase <br>";
                } else {
                    echo "Error al registrar la asistencia: " . $conn->error;
                }
            }
        } else {
            echo "No hay alumnos inscritos para la clase del día $fecha_clase <br>";
        }
    }
} else {
    echo "No hay clases programadas.";
}

// Cerrar conexión a la base de datos
$conn->close();
?>
