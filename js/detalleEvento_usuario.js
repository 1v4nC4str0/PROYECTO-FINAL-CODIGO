var popupBaja = document.getElementById('popup-baja');
        var openPopupBajaBtn = document.getElementById('boton-baja');
        var closeBtn = document.getElementsByClassName('close-btn');

        for (var i = 0; i < closeBtn.length; i++) {
            closeBtn[i].onclick = function () {
                this.closest('.popup').style.display = 'none';
                cancelar();
            }
        }

        window.onclick = function (event) {
            if (event.target == popupBaja) {
                event.target.style.display = 'none';
                cancelar();
            }
        }
        window.onload = function () {
            document.getElementById("errorTabla").style.display = 'none';
            document.getElementById('checkTodos').disabled = true;

            document.getElementById('checkTodos').addEventListener('change', function () {
                var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
                actualizarEstadoBotones();
            });

            var urlParams = new URLSearchParams(window.location.search);
            var evento_id = urlParams.get('evento_id');
            var evento_name = urlParams.get('evento_name');


            openPopupBajaBtn.disabled = true;
            openPopupBajaBtn.onclick = function () {
                var selectedCheckbox = document.querySelector('#cuerpo input[type="checkbox"]:checked');
                if (selectedCheckbox) {
                    popupBaja.style.display = 'flex';
                } else {
                    alert('Por favor, seleccione un registro para eliminar');
                }

            }
           
            if (evento_name) {
                document.getElementById('nombre-usuario-evento').textContent = 'Alumnos inscritos en el evento: ' + evento_name;
            }

            if (evento_id) {
                buscarUsuario_Evento(evento_id);
            }

        }

        function buscarUsuario_Evento(evento_id) {
            fetch('../../api/SBtokenProvider.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error al obtener token: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const token = data.token;

                    return fetch('../../api/SBbuscarUsuario_Evento.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'token': token
                        },
                        body: new URLSearchParams({
                            'evento_id': evento_id
                        })
                    });
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Response not OK: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const errorTabla = document.getElementById("errorTabla");
                    if (data.message && data.message.includes("No se encontró el registro")) {
                        errorTabla.style.display = 'flex';
                    } else {
                        mostrarUsuario(data);
                        errorTabla.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById("errorTabla").style.display = 'flex';
                })
                .finally(() => {
                    asignarEventosCheckbox();
                });
        }


        function mostrarUsuario(usuarios) {
            var cuerpo = document.getElementById("cuerpo");
            cuerpo.innerHTML = "";
            resultado = document.getElementById("cuerpo");
            resultados = resultado.querySelectorAll("td");

            var tabla = usuarios;
            for (let i = 0; i < tabla.length; i++) {
                var tr = document.createElement("tr");
                tr.id = i + 1;
                var box = document.createElement("td");
                var check = document.createElement("input");
                check.type = "checkbox";
                box.append(check);
                tr.append(box);
                td1 = document.createElement("td");
                td1.innerHTML = tabla[i].Evento_id;
                td2 = document.createElement("td");
                td2.innerHTML = tabla[i].Evento_nombre;
                td3 = document.createElement("td");
                td3.innerHTML = tabla[i].usuario_id;
                td4 = document.createElement("td");
                td4.innerHTML = tabla[i].Usuario_Nombre + ' ' + tabla[i].Usuario_Apellidos;
                td5 = document.createElement("td");
                var fecha = new Date(tabla[i].Evento_fecha);
                var dia = ('0' + fecha.getDate()).slice(-2);
                var mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
                var ano = fecha.getFullYear();
                td5.innerHTML = dia + '/' + mes + '/' + ano;

                tr.append(td1, td2, td3, td4, td5);
                resultado.append(tr);
            }
            asignarEventosCheckbox();
            actualizarCheckboxPrincipal();
        }

        function actualizarCheckboxPrincipal() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            var checkTodos = document.getElementById('checkTodos');
            checkTodos.disabled = checkboxes.length === 0;
            if (checkTodos.disabled) {
                checkTodos.checked = false;
            }
        }

        function asignarEventosCheckbox() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            var openPopupBajaBtn = document.getElementById('boton-baja');

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    var selected = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
                    openPopupBajaBtn.disabled = selected === 0;
                });
            });
        }

        function getIds() {
            var ids = [];
            var tabla = document.getElementById("tabla");
            var rows = tabla.getElementsByTagName("tr");
            for (let i = 1; i < rows.length; i++) {
                var checkbox = rows[i].querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    var eventIdCell = rows[i].cells[1];
                    var eventId = eventIdCell.textContent.trim();

                    var userIdCell = rows[i].cells[3];
                    var userId = userIdCell.textContent.trim();
                    ids.push({ eventId, userId });
                }
            }
            return ids;
        }

        function actualizarEstadoBotones() {
            var checkboxesSeleccionados = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
            var botonBaja = document.getElementById('boton-baja');

            botonBaja.disabled = checkboxesSeleccionados === 0;
        }

        function cancelar() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            popupBaja.style.display = 'none';

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            actualizarEstadoBotones();
        }

        function borrar() {
            var ids = getIds();
            if (ids.length > 0) {
                fetch('../../api/SBtokenProvider.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener token: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const token = data.token;

                        return ids.reduce((promiseChain, { eventId, userId }, index) => {
                            return promiseChain.then(() => {                       
                                const formData = new FormData();
                                formData.append('evento_id', eventId);
                                formData.append('usuario_id', userId);

                                return fetch('../../api/SBbajaUsuario_Evento.php', {
                                    method: 'POST',
                                    headers: {
                                        'token': token
                                    },
                                    body: formData
                                })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Response not OK: ' + response.statusText);
                                        }
                                        if (index === ids.length - 1) {
                                            window.location.reload();
                                        }
                                    });
                            });
                        }, Promise.resolve());
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("Error al eliminar registro(s).");
                    });
            } else {
                alert("No se ha seleccionado ningún registro para eliminar.");
            }
        }
        
        document.getElementById('btnAtras').addEventListener('click', function () {
            window.history.back();
        });