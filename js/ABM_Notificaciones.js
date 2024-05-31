var popupBaja = document.getElementById('popup-baja');
        var openPopupBajaBtn = document.getElementById('boton-baja');
        var closeBtn = document.getElementsByClassName('close-btn');

        openPopupBajaBtn.disabled = true;

        function asignarNotificacionesCheckbox() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    var seleccionados = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;

                    openPopupBajaBtn.disabled = seleccionados === 0;
                    document.getElementById('boton-detalle-evento').disabled = seleccionados !== 1;
                    document.getElementById('boton-detalle-actividades').disabled = seleccionados !== 1;
                    actualizarEstadoBotones();
                });
            });
        }


        openPopupBajaBtn.onclick = function () {
            var selectedCheckbox = document.querySelector('#cuerpo input[type="checkbox"]:checked');
            if (selectedCheckbox) {
                popupBaja.style.display = 'flex';
            } else {
                alert('Por favor, seleccione un registro para eliminar');
            }

        }


        for (var i = 0; i < closeBtn.length; i++) {
            closeBtn[i].onclick = function () {
                this.closest('.popup').style.display = 'none';
                cancelar();
            }
        }

        window.onclick = function (event) {
            if ( event.target == popupBaja) {
                event.target.style.display = 'none';
                cancelar();
            }
        }
 
        flatpickr("#fecha-rango", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es" 
        });

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

            var descripcion = sessionStorage.getItem('descripcion');
            var tipo = sessionStorage.getItem('tipo');
            
            var nombre = sessionStorage.getItem('nombre');
            var fecha_rango = sessionStorage.getItem('fecha-rango');

            if (descripcion || tipo || fecha_rango || nombre) {
                document.getElementById('descripcion').value = descripcion;
                document.getElementById('tipo').value = tipo;
                document.getElementById('fecha-rango').value = fecha_rango;
                document.getElementById('nombre').value = nombre;

                buscarNotificaciones();
            }

            sessionStorage.removeItem('descripcion');
            sessionStorage.removeItem('tipo');
            sessionStorage.removeItem('fecha-rango');
            sessionStorage.removeItem('nombre');

            asignarNotificacionesCheckbox();


        }

        function actualizarEstadoBotones() {
            var checkboxesSeleccionados = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
            var botonBaja = document.getElementById('boton-baja');
            var botonDetalleEvento = document.getElementById('boton-detalle-evento');
            var botonDetalleActividades = document.getElementById('boton-detalle-actividades');

            botonBaja.disabled = checkboxesSeleccionados === 0;
            botonDetalleEvento.disabled = checkboxesSeleccionados !== 1;
            botonDetalleActividades.disabled = checkboxesSeleccionados !== 1;
        }


        function buscarNotificaciones() {
            var fecha_rango = document.getElementById("fecha-rango").value;
            var descripcion = document.getElementById("descripcion").value;
            var tipo = document.getElementById("tipo").value;
            fetch('../../api/SBtokenProvider.php')
                .then(response => response.json())
                .then(data => {
                    const token = data.token;
                    const formData = new FormData();
                    formData.append('descripcion', descripcion);
                    formData.append('tipo', tipo);
                    formData.append('fecha_rango', fecha_rango);

                    const url = "../../api/SBbuscarNotificacion.php";
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'token': token
                        },
                        body: formData
                    });
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Response not OK: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    mostrarNotificaciones(data);
                })
                .catch(error => console.log('Error:', error));
        }


        function mostrarNotificaciones(registros) {
            var cuerpo = document.getElementById("cuerpo");
            cuerpo.innerHTML = "";
            resultado = document.getElementById("cuerpo");

            var tabla = registros;
            for (let i = 0; i < tabla.length; i++) {
                var tr = document.createElement("tr");

                var box = document.createElement("td");
                var check = document.createElement("input");
                check.type = "checkbox";
                box.append(check);
                tr.append(box);

                td1 = document.createElement("td");
                td1.innerHTML = tabla[i].notificacion_id;
                td2 = document.createElement("td");
                td2.innerHTML = tabla[i].notificacion_texto;
                td3 = document.createElement("td");
                td3.innerHTML = tabla[i].notificacion_tipo;
                td4 = document.createElement("td");
                td4.innerHTML = tabla[i].nombre_referencia;

                td5 = document.createElement("td");
                var fecha_rango = new Date(tabla[i].notificacion_creacion);
                var dia = ('0' + fecha_rango.getDate()).slice(-2);
                var mes = ('0' + (fecha_rango.getMonth() + 1)).slice(-2);
                var ano = fecha_rango.getFullYear();
                var hora = ('0' + fecha_rango.getHours()).slice(-2);
                var minutos = ('0' + fecha_rango.getMinutes()).slice(-2);
                td5.innerHTML = dia + '/' + mes + '/' + ano + ' ' + hora + ':' + minutos;

                tr.append(td1, td2, td3, td4, td5);
                resultado.append(tr);
            }
            asignarNotificacionesCheckbox();
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

        function getIds() {
            var ids = [];
            var tabla = document.getElementById("tabla");
            var rows = tabla.getElementsByTagName("tr");
            for (let i = 1; i < rows.length; i++) {
                var checkbox = rows[i].querySelector('input[type="checkbox"]');
                if (checkbox && checkbox.checked) {
                    var idCell = rows[i].cells[1];
                    var id = idCell.textContent;
                    ids.push(id);
                }
            }
            return ids;
        }

        function borrar() {
            var ids = getIds();
            if (ids.length > 0) {
                fetch('../../api/SBtokenProvider.php')
                    .then(response => response.json())
                    .then(data => {
                        const token = data.token;

                        return ids.reduce((promise, id) => {
                            return promise.then(() => {
                                const formData = new FormData();
                                formData.append('id', id);

                                return fetch('../../api/SBbajaNotis.php', {
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
                                        return response.text();
                                    });
                            });
                        }, Promise.resolve());
                    })
                    .then(() => {
                        console.log("Todos los eventos seleccionados han sido borrados.");
                        buscarNotificaciones();
                        popupBaja.style.display = 'none';
                    })
                    .catch(error => console.error('Error al borrar eventos:', error));
            } else {
                alert("No se ha seleccionado ningún evento para eliminar.");
            }
        }

        function cancelar() {
            var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
            popupBaja.style.display = 'none';

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            actualizarEstadoBotones();
        }

        function guardarValoresInputs() {
            sessionStorage.setItem('descripcion', document.getElementById('descripcion').value);
            sessionStorage.setItem('tipo', document.getElementById('tipo').value);
            sessionStorage.setItem('fecha-rango', document.getElementById('fecha-rango').value);
        }

        function irADetalles() {
            var selectedCheckbox = document.querySelector('#cuerpo input[type="checkbox"]:checked');
            if (selectedCheckbox) {
                var evento_id = selectedCheckbox.closest('tr').getElementsByTagName('td')[1].textContent;
                var evento_name = selectedCheckbox.closest('tr').getElementsByTagName('td')[2].textContent;

                guardarValoresInputs();
                window.location.href = 'detalleEvento_usuario.html?evento_id=' + evento_id + '&evento_name=' + evento_name;
            } else {
                alert('Por favor, seleccione un registro');
            }
        }