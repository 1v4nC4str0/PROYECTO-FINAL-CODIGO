var identificador = document.getElementById("identificador").value;

console.log("Estamos aqui: " + identificador);

//ABM TIPO GRADO

if (identificador=="TGrado") {
    var tabla = document.getElementById('cuerpo');
    var botonBusqueda = document.getElementById('filtro-tipoGrado');
    
    botonBusqueda.addEventListener("click", function(e){

        console.log("hola");
    })

}

