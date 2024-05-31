 let token;
        document.addEventListener("DOMContentLoaded", async function () {
            inicializarEventos();
            document.getElementById('btnAtras').addEventListener('click', function () {
                window.history.back();
            });
        });

        function inicializarEventos() {
            document.getElementById('check-todos').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                actualizarEstadoBotones();
            });
            document.getElementById('nombre').addEventListener('input', buscarTipoActividad);
            document.getElementById('boton-mod').addEventListener('click', actualizarTipoActividad);
            document.getElementById('boton-alta').addEventListener('click', openPopupAlta);
            document.getElementById('boton-baja').addEventListener('click', openPopupBaja);
            document.getElementById("boton-modificar").addEventListener('click', openPopupModificar);


        }


        function openPopupBaja() {
            if (document.querySelector('#cuerpo input[type="checkbox"]:checked')) {
                document.getElementById('popup-baja').style.display = 'flex';
            } else {
                alert("Por favor, seleccione un registro a eliminar");
            }
        }

        function openPopupAlta() {
            if (!this.disabled) {
                document.getElementById('popup-alta').style.display = 'flex';
            }
            document.getElementById('url-popup-alta').addEventListener('input', function () {
                verificarUrl("img-popup-alta", this.value);
            });
        }
        function openPopupModificar() {
            const popupModificar = document.getElementById('popup-modificar');
            var checkSelect = document.querySelector('#cuerpo input[type="checkbox"]:checked');
            if (checkSelect) {
                var row = checkSelect.closest('tr');
                var cells = row.getElementsByTagName('td');

                document.getElementById("nombre-popupModf").value = cells[2].textContent;
                popupModificar.style.display = 'flex';
            }

        }

        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                this.closest('.popup').style.display = 'none';
            });
        });


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
            if (event.target == document.getElementById("popup-baja")) {
                document.getElementById("popup-baja").style.display = 'none';
                cancelar();
            }
            if (event.target == document.getElementById("popup-alta")) {
                document.getElementById("popup-alta").style.display = 'none';
                cancelar();
            }
            if (event.target == document.getElementById("popup-modificar")) {
                document.getElementById("popup-alta").style.display = 'none';
                cancelar();
            }
        }


        async function buscarTipoActividad() {
            const nombre = document.getElementById('nombre').value;

            const fetchTipoActividad = async () => {
                try {
                    const tokenResponse = await fetch('../../api/tokenProvider.php');
                    if (!tokenResponse.ok) {
                        throw new Error(`Error obteniendo el token: ${tokenResponse.statusText}`);
                    }

                    const tokenData = await tokenResponse.json();
                    token = tokenData.token;
                    console.log("Token recibido para consulta tipo de actividad al darle a la lupa");

                    const formData = new FormData();
                    formData.append('nombre', nombre);

                    const url = "../../api/SBConsultaTipoActividad.php";
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'token': token
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`No ok ${response.statusText}`);
                    }

                    const data = await response.json();
                    console.log
                    mostrar(data);
                } catch (error) {
                    console.log(error);
                }
            };
            fetchTipoActividad();
            actualizarEstadoBotones();
        };



        //INSERTAR TIPO ACTIVIDAD
        async function altaTipoActividad() {
            try {
                const tokenResponse = await fetch('../../api/tokenProvider.php');
                if (!tokenResponse.ok) {
                    throw new Error(`Error obteniendo el token: ${tokenResponse.statusText}`);
                }

                const tokenData = await tokenResponse.json();
                const token = tokenData.token;
                console.log("Token recibido para alta tipo de actividad");

                var name = document.getElementById("nombre-popup").value;

                var data = new FormData();
                data.append('name', name);

                const altaResponse = await fetch("../../api/SBAltaTipoActividad.php", {
                    method: 'POST',
                    headers: {
                        'token': token
                    },
                    body: data
                });

                if (!altaResponse.ok) {
                    throw new Error(`Error en la solicitud de alta: ${altaResponse.statusText}`);
                }
                console.log("Alta exitosa");
                buscarTipoActividad();
            } catch (error) {
                console.error(error);
            } finally {
                document.getElementById("popup-alta").style.display = 'none';
            }
            actualizarEstadoBotones();
        };

        async function borrarTipoActividad() {
            var ids = obtenerIDporCheck();
            if (ids.length > 0) {
                try {
                    const tokenResponse = await fetch('../../api/tokenProvider.php');
                    if (!tokenResponse.ok) {
                        throw new Error(`Error obteniendo el token: ${tokenResponse.statusText}`);
                    }

                    const tokenData = await tokenResponse.json();
                    const token = tokenData.token;
                    console.log("Token recibido para borrar tipo de actividad");

                    const promises = ids.map(async (id) => {
                        const data = new FormData();
                        data.append('id', id);

                        const bajaResponse = await fetch("../../api/SBbajaTipoActividad.php", {
                            method: 'POST',
                            headers: {
                                'token': token
                            },
                            body: data
                        });

                        if (!bajaResponse.ok) {
                            throw new Error(`Error en la solicitud de baja: ${bajaResponse.statusText}`);
                        }
                        buscarTipoActividad();
                        document.getElementById("popup-baja").style.display = 'none';
                        console.log(`Tipo de actividad con ID ${id} eliminado correctamente`);
                    });

                    await Promise.all(promises);


                } catch (error) {
                    console.error(error);
                }
            } else {
                alert("No se ha encontrado el elemento");
            }
            actualizarEstadoBotones();
        }

        async function actualizarTipoActividad() {
            var popup_modificar = document.getElementById('popup-modificar');
            var nombre = document.getElementById('nombre-popupModf').value;
            var ids = obtenerIDporCheck();
            var id = ids[0];

            try {
                if (!token) {
                    await obtenerToken();
                }

                var formData = new FormData();

                formData.append('id', id);
                formData.append('tipo_actividad_nombre', nombre);


                try {
                    const response = await fetch("../../api/SBModificarTipoActividad.php", {
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
                        alert('Tipo actividad actualizada exitosamente');
                    } else {
                        alert('Error al actualizar tipo actividad: ' + data.message);
                    }
                } finally {
                    buscarTipoActividad();
                    limpiar();
                    document.getElementById("popup-modificar").style.display = 'none';
                }

            } catch (error) {
                console.error('Error al actualizar el tipo de actividad:', error);
            }
            actualizarEstadoBotones();
        }






        function mostrar(objeto) {
            console.log(JSON.stringify(objeto));
            resultado = document.getElementById("cuerpo");
            resultados = resultado.querySelectorAll("td");
            for (let i = 0; i < resultados.length; i++) {
                resultados[i].remove();

            }
            var tabla = objeto;
            console.log(tabla[1]);
            for (let i = 0; i < tabla.length; i++) {
                var tr = document.createElement("tr");
                tr.id = i + 1;
                var box = document.createElement("td");
                var check = document.createElement("input");
                check.type = "checkbox";
                box.append(check);
                tr.append(box);
                td1 = document.createElement("td");
                td1.innerHTML = tabla[i].Tipo_Actividad_ID;

                td2 = document.createElement("td");
                td2.innerHTML = tabla[i].Tipo_Actividad_nombre;

                tr.append(td1, td2);
                resultado.append(tr);
            }
            asignarTipoActividadCheckbox();
            actualizarCheckboxPrincipal();
        }

        function actualizarCheckboxPrincipal() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            var checkTodos = document.getElementById('check-todos');
            checkTodos.disabled = checkboxes.length === 0;
            if (checkTodos.disabled) {
                checkTodos.checked = false;
            }
        }

        function asignarTipoActividadCheckbox() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            console.log("checkboxes: ", checkboxes.length);

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    var selected = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
                    document.getElementById('boton-baja').disabled = selected === 0;
                    actualizarEstadoBotones();

                });
            });
        }
        function actualizarEstadoBotones() {
            var checkSelect = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
            var botonBaja = document.getElementById('boton-baja');
            botonBaja.disabled = checkSelect === 0;
            document.getElementById('boton-modificar').disabled = checkSelect !== 1;

        }


        function obtenerIDporCheck() {

            var ids = [];
            var tabla = document.getElementById("tabla");
            var filas = tabla.getElementsByTagName("tr");

            for (let i = 1; i < filas.length; i++) {
                var checkbox = filas[i].querySelector('input[type="checkbox"]');
                console.log(checkbox);
                if (checkbox && checkbox.checked) {
                    var idAct = filas[i].cells[1];
                    var id = idAct.textContent;
                    ids.push(id);
                }
            }
            return ids;
        }

        function cancelar() {
            var checks = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            document.querySelectorAll('.popup').forEach(popup => {
                popup.style.display = 'none';
            });
            buscarTipoActividad();
            limpiar();
            checks.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            actualizarEstadoBotones();
        }
        function limpiar() {
            document.getElementById("nombre-popup").value = "";

        }
        function navegar(url) {
            window.location = url;
        }