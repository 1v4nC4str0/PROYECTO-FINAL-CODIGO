   let token;
        document.addEventListener("DOMContentLoaded", async function () {
            await obtenerToken();
            inicializarEventos();
            fetchTipoActividad("tipo_act_ent");
            document.getElementById("boton-modificar").disabled = true;
            document.getElementById("boton-baja").disabled = true;
            restoreSessionValues();
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

            document.getElementById('boton-alta').addEventListener('click', openPopupAlta);
            document.getElementById('boton-baja').addEventListener('click', openPopupBaja);
            document.getElementById('boton-modificar').addEventListener('click', openPopupModificar);
            document.getElementById('btn-mod').addEventListener('click', actualizarActividad);
            document.getElementById('boton-alta-act').addEventListener('click', altaActividad);
            document.getElementById("errorTabla").style.display = 'none';
        }



        async function fetchTipoActividad(selectId) {
            try {
                const url = "../../api/SBConsultaTipoActividad.php";
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'token': token }
                });

                if (!response.ok) {
                    throw new Error(`No ok ${response.statusText}`);
                }

                const data = await response.json();
                const selectElement = document.getElementById(selectId);

                if (selectElement) {
                    selectElement.innerHTML = "";

                    const defaultOption = document.createElement("option");
                    defaultOption.value = "";
                    defaultOption.textContent = "Seleccione un tipo";
                    selectElement.appendChild(defaultOption);

                    data.forEach(item => {
                        var opcion = document.createElement("option");
                        opcion.value = item.Tipo_Actividad_ID;
                        opcion.textContent = item.Tipo_Actividad_nombre;
                        selectElement.append(opcion);
                    });

                    var checkSelect = document.querySelector('#cuerpo input[type="checkbox"]:checked');
                    if (checkSelect) {
                        var row = checkSelect.closest('tr');
                    document.getElementById("tipo_act_mod").value = row.getAttribute('data');
                    }

                } else {
                    console.error(`Elemento con ID ${selectId} no encontrado en el DOM.`);
                }
            } catch (error) {
                console.error('Error al obtener tipos de actividad:', error);
            }

        };

        function restoreSessionValues() {
            const nombre = sessionStorage.getItem('nombre');
            const Actividad_descripcion = sessionStorage.getItem('Actividad_descripcion');
            const tipo_actividad_nombre = sessionStorage.getItem('tipo_act_ent');

            if (nombre !== null) document.getElementById('nombre').value = nombre;
            if (Actividad_descripcion !== null) document.getElementById('Actividad_descripcion').value = Actividad_descripcion;
            if (tipo_actividad_nombre !== null) document.getElementById('tipo_act_ent').value = tipo_actividad_nombre;

            }

        

        async function buscarActividad() {
            const nombre = document.getElementById('nombre').value;
            const descripcion = document.getElementById('Actividad_descripcion').value;
            const tipo_nombre = document.getElementById('tipo_act_ent').value;


                // Guardar los valores en sessionStorage
                sessionStorage.setItem('nombre', nombre);
                sessionStorage.setItem('Actividad_descripcion', descripcion);
                sessionStorage.setItem('tipo_act_ent', tipo_nombre);

            try {
                if (!token) {
                    throw new Error('Token no disponible. Por favor, recargue la página.');
                }

                await fetchTipoActividad('tipo_act_ent');

                const formData = new FormData();
                formData.append('nombre', nombre);
                formData.append('descripcion', descripcion);
                formData.append('tipo_act_ent', tipo_nombre);

                // Realizar la solicitud a la API
                const apiResponse = await fetch('../api/SBconsultaActividad.php', {
                    method: 'POST',
                    headers: {
                        'token': token
                    },
                    body: formData
                });

                if (!apiResponse.ok) {
                    throw new Error(`Error en la API: ${apiResponse.statusText}`);
                }

                const data = await apiResponse.json();
                mostrar(data);
                console.log(data);

            } catch (error) {
                console.log('Error:', error);
                const errorTabla = document.getElementById("errorTabla");
                errorTabla.style.display = 'flex';

                if (error instanceof TypeError && error.message === 'Failed to fetch') {
                    errorTabla.innerText = 'No se pudo conectar con el servidor. Por favor, revise su conexión a Internet.';
                } else {
                    errorTabla.innerText = 'Hubo un error al buscar las actividades. Por favor, intente de nuevo.';
                }
            }
        }

        function mostrar(objeto) {
            resultado = document.getElementById("cuerpo");
            resultado.innerHTML = '';
            resultados = resultado.querySelectorAll("td");

            var tabla = objeto;
            for (let i = 0; i < tabla.length; i++) {
                var tr = document.createElement("tr");
                tr.id = i + 1;
                tr.setAttribute("data", tabla[i].tipo_actividad_id)
                var box = document.createElement("td");
                var check = document.createElement("input");
                check.type = "checkbox";
                box.append(check);
                tr.append(box);
                td1 = document.createElement("td");
                td1.innerHTML = tabla[i].Actividad_ID;
                td1.className = "a1";
                td2 = document.createElement("td");
                td2.innerHTML = tabla[i].Actividad_nombre;
                td3 = document.createElement("td");
                td3.innerHTML = tabla[i].Actividad_descripcion;
                td4 = document.createElement("td");
                td4.innerHTML = tabla[i].Actividad_foto;
                td5 = document.createElement("td");
                td5.innerHTML = tabla[i].Tipo_Actividad_nombre;
                td5.className = i+1;
                td5.id = tabla[i].tipo_actividad_id;
                tr.append(td1, td2, td3, td4, td5);
                resultado.append(tr);

            }
            asignarActividadCheckbox();
            actualizarCheckboxPrincipal();
        }


        function asignarActividadCheckbox() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    checkbox.addEventListener('change', actualizarEstadoBotones());
                });
            });
        }

        function actualizarCheckboxPrincipal() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            var checkTodos = document.getElementById('check-todos');
            checkTodos.disabled = checkboxes.length === 0;
            if (checkTodos.disabled) {
                checkTodos.checked = false;
            }
        }

        function openPopupAlta() {
            let popupAlta = document.getElementById('popup-alta');
            if (!this.disabled) {
                popupAlta.style.display = 'flex';
                document.getElementById('url-popup-alta').addEventListener('input', function () {
                    verificarUrl("img-popup-alta", this.value);
                });
            }
            fetchTipoActividad("tipo_act_alta");
        }

        function openPopupBaja() {
            let popupBaja = document.getElementById('popup-baja');
            if (document.querySelector('#cuerpo input[type="checkbox"]:checked')) {
                popupBaja.style.display = 'flex';
            } else {
                alert("Por favor, seleccione un registro a eliminar");
            }
        }

        function openPopupModificar() {
            const popupModificar = document.getElementById('popup-modificar');
            var checkSelect = document.querySelector('#cuerpo input[type="checkbox"]:checked');
            if (checkSelect) {

                var row = checkSelect.closest('tr');
                var cells = row.getElementsByTagName('td');
                console.log(row.id)
                
                fetchTipoActividad("tipo_act_mod"); 
                document.getElementById("nombre-popupModf").value = cells[2].textContent;
                document.getElementById("descripcion-popupModf").value = cells[3].textContent;
                document.getElementById("url-popupModf").value = cells[4].textContent;
                document.getElementById("img-popup-modf").src = cells[4].textContent || "view/img/imgen.jpeg";
                document.getElementById("tipo_act_mod").value = row.getAttribute("data");
                console.log(row.getAttribute("data"));

                document.getElementById("url-popupModf").addEventListener('input', function () {
                    verificarUrl("img-popup-modf", this.value);
                });

                
                popupModificar.style.display = 'flex';
          //      buscarActividad();

            } else {
                alert('Por favor, seleccione un registro para modificar');
            }
        }

        function verificarUrl(id, newUrl) {
            var imagenPredeterminada = "view/img/imgen.jpeg";
            var nuevaUrl = newUrl;

            if (nuevaUrl.match(/\.(jpeg|jpg|gif|png)$/)) {
                document.getElementById(id).src = nuevaUrl;
            } else {
                document.getElementById(id).src = imagenPredeterminada;
            }
        }



        window.onclick = function (event) {
            if (event.target == document.getElementById("popup-modificar")) {
                document.getElementById("popup-modificar").style.display = 'none';
                cancelar();
            }
            if (event.target == document.getElementById("popup-baja")) {
                document.getElementById("popup-baja").style.display = 'none';
                cancelar();
            }
            if (event.target == document.getElementById("popup-alta")) {
                document.getElementById("popup-alta").style.display = 'none';
                cancelar();
            }
        }
        closeBtn = document.querySelectorAll(".close-btn");
        for (var i = 0; i < closeBtn.length; i++) {
            closeBtn[i].onclick = function () {
                this.closest('.popup').style.display = 'none';
                cancelar();
            }
        }


        function actualizarEstadoBotones() {
            var checkSelect = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
            document.getElementById('boton-modificar').disabled = checkSelect !== 1;
            document.getElementById('boton-baja').disabled = checkSelect === 0;
            document.getElementById('boton-detalle').disabled = checkSelect !== 1;
        }


        async function altaActividad() {
            var nombre = document.getElementById("nombre-alta").value;
            var descripcion = document.getElementById("descripcion-alta").value;
            var foto = document.getElementById("url-popup-alta").value;
            var tipo = document.getElementById('tipo_act_alta').value;

            if (!nombre || !descripcion || !foto || !tipo) {
                alert("Por favor, rellene todos los campos");
                return;
            }

            try {
                const formData = new FormData();
                formData.append('name', nombre);
                formData.append('descripcion', descripcion);
                formData.append('foto', foto);
                formData.append('tipactid', tipo);

                const response = await fetch('../../api/SBAltaActividad.php', {
                    method: 'POST',
                    headers: {
                        'token': token
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.statusText);
                }

                limpiar();
                document.getElementById('popup-alta').style.display = 'none';
                buscarActividad();
                


            } catch (error) {
                console.error('Error en alta de actividad:', error);
            }
        }

        function obtenerIDporCheck() {
            var ids = [];
            var tabla = document.getElementById("tabla");
            var filas = tabla.getElementsByTagName("tr");

            for (let i = 1; i < filas.length; i++) {
                var checkbox = filas[i].querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    var idAct = filas[i].cells[1];
                    var id = idAct.textContent;
                    ids.push(id);
                }
            }
            return ids;
        }

        //FC BORRAR ACTIVIDAD -->
        async function borrarActividad() {
            var ids = obtenerIDporCheck();
            if (ids.length > 0) {
                try {
                    for (const id of ids) {
                        var data = new FormData();
                        data.append('id', id);

                        const response = await fetch("../api/SBbajaActividad.php", {
                            method: 'POST',
                            headers: {
                                'token': token
                            },
                            body: data
                        });

                        if (!response.ok) {
                            throw new Error(`Error en la API: ${response.statusText}`);
                        }

                        buscarActividad();

                        document.getElementById('popup-baja').style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error al borrar la actividad:', error);
                    alert('Hubo un error al borrar la actividad. Por favor, intente de nuevo.');
                }

            }
        }


        //MODIFICACION DE UNA ACTIVIDAD
        async function actualizarActividad() {
            var popupModificar = document.getElementById('popup-modificar');
            var nombre = document.getElementById('nombre-popupModf').value;
            var desc = document.getElementById('descripcion-popupModf').value;
            var foto = document.getElementById('url-popupModf').value;
            var tipo = document.getElementById('tipo_act_mod').value;
            var ids = obtenerIDporCheck();
            var id = ids[0];

            try {
                if (!token) {
                    await obtenerToken();
                }

                var formData  = new FormData();

                formData.append('id', id);
                formData.append('nombre', nombre);
                formData.append('descripcion', desc);
                formData.append('foto', foto);
                formData.append('tipactid', tipo)

                console.log("Datos enviados:", { id, nombre, desc, foto, tipo });

                try {
                    const response = await fetch("../../api/SBModificarActividad.php", {
                        method: 'POST',
                        headers: {
                            'token': token
                        },
                        body: formData 
                    });

                } catch {
                    if (!response.ok) {
                        throw new Error(`Error en la API: ${response.statusText}`);
                    }

                    const data = await response.json();
                    if (data.success) {
                        alert('Actividad actualizada exitosamente');
                    } else {
                        alert('Error al actualizar actividad: ' + data.message);
                    }
                } finally {
                    buscarActividad();
                    limpiar();
                    popupModificar.style.display = 'none';
                }

            } catch (error) {
                console.error('Error al actualizar la actividad:', error);
            }
        }


        function detalles() {
            var checkSelect = document.querySelector('#cuerpo input[type="checkbox"]:checked');
            if (checkSelect) {
                var actividad_id = checkSelect.closest('tr').getElementsByTagName('td')[1].textContent;
                var actividad_nombre = checkSelect.closest('tr').getElementsByTagName('td')[2].textContent;
                guardarValoresInputs();

                window.location.href = 'detalleActividad_usuario.html?Actividad_ID=' + actividad_id + '&Actividad_Nombre=' + actividad_nombre;
            } else {
                alert('Por favor, seleccione un registro');
            }
        }


        function guardarValoresInputs() {
            sessionStorage.setItem('nombre', document.getElementById('nombre').value);
            sessionStorage.setItem('Actividad_descripcion', document.getElementById('Actividad_descripcion').value);
            sessionStorage.setItem('tipo_act_ent', document.getElementById('tipo_act_ent').value);

        }
        function cancelar() {
            var checks = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            document.getElementById("popup-baja").style.display = "none";
            document.getElementById("popup-alta").style.display = "none";
            document.getElementById("popup-modificar").style.display = "none";
            limpiar();
            checks.forEach(function (checkbox) {
                checkbox.checked = false;
            })
            actualizarEstadoBotones();
        }


        function limpiar() {
            document.getElementsByName("nombre-popup").value = "";
            document.getElementsByName("descripcion-popup").value = "";
            document.getElementsByName("url-popup").value = "";

                sessionStorage.removeItem('nombre');
                sessionStorage.removeItem('Actividad_descripcion');
                sessionStorage.removeItem('tipo_act_ent');
        }
        function navegar(url) {
            window.location = url;
        }