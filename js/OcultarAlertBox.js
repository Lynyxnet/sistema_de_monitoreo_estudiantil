//Funcion para ocultar el mensaje despues de 3 segundos
setTimeout(function(){
    var alertBoxes = document.querySelectorAll('.alert');
    alertBoxes.forEach(function(alertBox){
        alertBox.style.display = 'none'; //Oculta el div cambiando su estilo 'display' a 'none'
    });
}, 4000) // 3000 milisegundos = 3 segundos