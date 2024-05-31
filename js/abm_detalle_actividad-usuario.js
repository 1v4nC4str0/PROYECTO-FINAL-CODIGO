 let token;
    document.addEventListener("DOMContentLoaded", async function () {
        await obtenerToken();
        inicializarEventos();
        buscarUsuarioActividad();
        document.getElementById("boton-baja").disabled = true;
    });

    async function obtenerToken() {
        try {
            const tokenResponse = await fetch('../../api/tokenProvider.php');

            if (!tokenResponse.ok) {
                throw new Error(`Error obteniendo el token: ${tokenResponse.statusText}`);
            }
            const tokenData = await tokenResponse.json();
            token = tokenData.token;
            console.log("Token recibido para consulta de actividad:");
        } catch (error) {
            console.error('Error al obtener el token:', error);

        }
    }

    function inicializarEventos() {
        document.getElementById('check-todos').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            actualizarEstadoBotones();
        });

        document.getElementById('boton-baja').addEventListener('click', openPopupBaja);
    }


    closeBtn = document.querySelectorAll(".close-btn");
    for (var i = 0; i < closeBtn.length; i++) {
        closeBtn[i].onclick = function () {
            this.closest('.popup').style.display = 'none';
            cancelar();
        }
    }
    document.getElementById('btnAtras').addEventListener('click', function () {
        window.history.back();
    });

    window.onclick = function (event) {
        if (event.target == document.getElementById("popup-baja")) {
            event.target.style.display = 'none';
            cancelar();
        }
    }

    function openPopupBaja() {
        let popupBaja = document.getElementById('popup-baja');
        if (document.querySelector('#cuerpo input[type="checkbox"]:checked')) {
            popupBaja.style.display = 'flex';
        } else {
            alert("Por favor, seleccione un registro a eliminar");
        }
    }



    //Título según la actividad seleccionada
    const urlParams = new URLSearchParams(window.location.search);
    var actividad_ID = urlParams.get("Actividad_ID");
    var actividad_nombre = urlParams.get("Actividad_Nombre");
    if (actividad_nombre) {
        document.getElementById("nombre_Act").textContent = "Usuarios de la Actividad " + actividad_nombre;
    }




    function asignarActividadCheckbox() {
        var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                checkbox.addEventListener('change', actualizarEstadoBotones());
            });
        });
    }

    function actualizarEstadoBotones() {
        var checkSelect = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
        document.getElementById('boton-baja').disabled = checkSelect === 0;
    }


    function actualizarCheckboxPrincipal() {
        var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
        var checkTodos = document.getElementById('check-todos');
        checkTodos.disabled = checkboxes.length === 0;
        if (checkTodos.disabled) {
            checkTodos.checked = false;
        }
    }



    function cancelar() {
        var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
        document.querySelector(".popup").style.display = 'none';

        checkboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });
        actualizarEstadoBotones();
    }

    async function buscarUsuarioActividad() {
        try {
            if (!token) {
                throw new Error('Token no disponible. Por favor, recargue la página.');
            }
            const urlParams = new URLSearchParams(window.location.search);
            const actividad_ID = urlParams.get("Actividad_ID");

            if (!actividad_ID) {
                throw new Error('ID de actividad no encontrado en la URL.');
            }
            console.log("Actividad ID:", actividad_ID);


            const response =
                await fetch('../api/SBbuscarUsuario-Actividad.php', {
                    method: 'POST',
                    headers: {
                        'token': token,
                    },
                    body: new URLSearchParams({
                        'actividad_id': actividad_ID
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Error en la API: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.message && data.message.includes("No se encontró el registro")) {
                        } else {
                            if(!mostrarUsuario(data)) {
                                document.getElementById("mensaje").style.display = "block";
                                document.getElementById("mensaje").innerHTML = 'No se encontraron usuarios inscritos';
                            } else{
                                document.getElementById("mensaje").style.display = "none";
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);                        
                    })
        } catch (error) {
            console.error('Error al buscar usuarios de la actividad:', error);
        }
    }


    function mostrarUsuario(usuarios) {
        var cuerpo = document.getElementById("cuerpo");
        cuerpo.innerHTML = "";
        resultado = document.getElementById("cuerpo");
        resultados = resultado.querySelectorAll("td");

        var tabla = usuarios;
        if (usuarios.length === 0) {
            return false;
        }
        for (let i = 0; i < tabla.length; i++) {
            var tr = document.createElement("tr");
            tr.id = i + 1;
            var box = document.createElement("td");
            var check = document.createElement("input");
            check.type = "checkbox";
            box.append(check);
            tr.append(box);
            td1 = document.createElement("td");
            td1.innerHTML = tabla[i].actividad_ID;
            td2 = document.createElement("td");
            td2.innerHTML = tabla[i].Actividad_nombre;
            td3 = document.createElement("td");
            td3.innerHTML = tabla[i].usuario_id;
            td4 = document.createElement("td");
            td4.innerHTML = tabla[i].Usuario_Nombre + ' ' + tabla[i].Usuario_Apellidos;


            tr.append(td1, td2, td3, td4);
            resultado.append(tr);
        }
        asignarActividadCheckbox();
        actualizarCheckboxPrincipal();
        return true;
    }


    async function borrarActividadUsuario() {
        var ids = obtenerIDporCheck();
        var actividad_id = urlParams.get('Actividad_ID');
        if (ids.length > 0) {
            try {
                for (const id of ids) {
                    var data = new FormData();
                    data.append('actividad_id', actividad_id)
                    data.append('usuario_id', id);

                    const response = await fetch("../api/SBbajaUsuario-Actividad.php", {
                        method: 'POST',
                        headers: {
                            'token': token
                        },
                        body: data
                    });

                    if (!response.ok) {
                        throw new Error(`Error en la API: ${response.statusText}`);
                    }
                    buscarUsuarioActividad();
                    document.getElementById("popup-baja").style.display = 'none';
                    actualizarEstadoBotones();
                }
            } catch (error) {
                console.error('Error al borrar el usuario de la actividad:', error);
                alert('Hubo un error al borrar el usuario de la actividad. Por favor, intente de nuevo.');
            }
        } else {
            alert("No se ha encontrado el elemento");
        }
    }


    function obtenerIDporCheck() {

        var ids = [];
        var tabla = document.getElementById("tabla");
        var filas = tabla.getElementsByTagName("tr");

        for (let i = 1; i < filas.length; i++) {
            var checkbox = filas[i].querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                var idAct = filas[i].cells[3];
                var id = idAct.textContent;
                ids.push(id);
            }
        }
        return ids;
    }
    function navegar(url) {
            window.location = url;
        }