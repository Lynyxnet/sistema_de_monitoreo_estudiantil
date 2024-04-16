function limpiarDatos(){
    // document.getElementById("archivo").value = "";
    //el metodo "value" limpia el valor del "input file", elimina cualquier archivo seleccionado
    document.querySelectorAll("#asignatura, #semestre, #diasInicio, #diasFinal, #miArchivo").forEach(elemento => elemento.value = "");
}