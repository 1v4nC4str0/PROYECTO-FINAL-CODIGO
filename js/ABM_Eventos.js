var popupModificar = document.getElementById('popup-modificar');
var popupBaja = document.getElementById('popup-baja');
var popupAlta = document.getElementById('popup-alta');
var openPopupModificarBtn = document.getElementById('boton-modificar');
var openPopupBajaBtn = document.getElementById('boton-baja');
var openPopupAltaBtn = document.getElementById('boton-alta');
var closeBtn = document.getElementsByClassName('close-btn');

openPopupModificarBtn.disabled = true;
openPopupBajaBtn.disabled = true;

function asignarEventosCheckbox() {
    var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var seleccionados = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;

            openPopupModificarBtn.disabled = seleccionados !== 1;
            openPopupBajaBtn.disabled = seleccionados === 0;
            document.getElementById('boton-detalle').disabled = seleccionados !== 1;
            actualizarEstadoBotones();
        });
    });
}

openPopupModificarBtn.onclick = function () {
    var selectedCheckbox = document.querySelector('#cuerpo input[type="checkbox"]:checked');
    if (selectedCheckbox) {

        var row = selectedCheckbox.closest('tr');
        var cells = row.getElementsByTagName('td');
        var fecha = cells[5].textContent;
        var partes = fecha.split('/');
        var dia = partes[0];
        var mes = partes[1];
        var ano = partes[2];

        var horaCompleta = cells[6].textContent.split(':');
        var horaFormateada = horaCompleta[0] + ':' + horaCompleta[1];

        document.getElementById("nombre-popupModf").value = cells[2].textContent;
        document.getElementById("descripcion-popupModf").value = cells[3].textContent;
        document.getElementById("lugar-popupModf").value = cells[4].textContent;
        document.getElementById("fecha-popupModf").value = `${ano}-${mes}-${dia}`;
        document.getElementById("hora-popupModf").value = horaFormateada;
        document.getElementById("formato-popupModf").value = cells[7].textContent;
        document.getElementById("url-popupModf").value = cells[8].textContent;

        document.getElementById("img-popup-modf").src = cells[8].textContent || "view/img/imgen.jpeg";

        document.getElementById("url-popupModf").addEventListener('input', function () {
            verificarUrl("img-popup-modf", this.value);
        });


        popupModificar.style.display = 'flex';
    } else {
        alert('Por favor, seleccione un registro para modificar');
    }

}

openPopupBajaBtn.onclick = function () {
    var selectedCheckbox = document.querySelector('#cuerpo input[type="checkbox"]:checked');
    if (selectedCheckbox) {
        popupBaja.style.display = 'flex';
    } else {
        alert('Por favor, seleccione un registro para eliminar');
    }

}

openPopupAltaBtn.onclick = function () {
    if (!this.disabled) {
        popupAlta.style.display = 'flex';
        document.getElementById('url-popup-alta').addEventListener('input', function () {
            verificarUrl("img-popup-alta", this.value);
        });
        limpiar();
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

for (var i = 0; i < closeBtn.length; i++) {
    closeBtn[i].onclick = function () {
        this.closest('.popup').style.display = 'none';
        cancelar();
    }
}

window.onclick = function (event) {
    if (event.target == popupModificar || event.target == popupBaja || event.target == popupAlta) {
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

    var nombre = sessionStorage.getItem('nombre');
    var descripcion = sessionStorage.getItem('descripcion');
    var lugar = sessionStorage.getItem('lugar');
    var fecha = sessionStorage.getItem('fecha');
    var formato = sessionStorage.getItem('formato');

    if (nombre || descripcion || lugar || fecha || formato) {
        document.getElementById('nombre').value = nombre;
        document.getElementById('descripcion').value = descripcion;
        document.getElementById('lugar').value = lugar;
        document.getElementById('fecha').value = fecha;
        document.getElementById('formato').value = formato;

        buscarEvento();
    }

    sessionStorage.removeItem('nombre');
    sessionStorage.removeItem('descripcion');
    sessionStorage.removeItem('lugar');
    sessionStorage.removeItem('fecha');
    sessionStorage.removeItem('formato');

    asignarEventosCheckbox();


}

function actualizarEstadoBotones() {
    var checkboxesSeleccionados = document.querySelectorAll('#cuerpo input[type="checkbox"]:checked').length;
    var botonModificar = document.getElementById('boton-modificar');
    var botonBaja = document.getElementById('boton-baja');
    var botonDetalle = document.getElementById('boton-detalle');

    botonModificar.disabled = checkboxesSeleccionados !== 1;
    botonBaja.disabled = checkboxesSeleccionados === 0;
    botonDetalle.disabled = checkboxesSeleccionados !== 1;
}

function altaEvento() {
    var popupAlta = document.getElementById('popup-alta');
    var name = document.getElementById("nombre-popup").value;
    var fecha = document.getElementById("fecha-popup").value;
    var descripcion = document.getElementById("descripcion-popup").value;
    var foto = document.getElementById("url-popup-alta").value;
    var lugar = document.getElementById("lugar-popup").value;
    var hora = document.getElementById("hora-popup").value;
    var formato = document.getElementById("formato-popup").value;
    var data = new FormData();

    if (!validarFecha(fecha)) {
        alert('La fecha introducida no es válida. Por favor, introduzca una fecha en formato DD/MM/AAAA.');
        return;
    }

    if (!validarHora(hora)) {
        alert('La hora introducida no es válida. Por favor, introduzca una hora en formato HH:MM.');
        return;
    }

    fetch('../../api/SBtokenProvider.php')
        .then(response => response.json())
        .then(data => {
            const token = data.token;

            const formData = new FormData();
            formData.append('name', name);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('foto', foto);
            formData.append('lugar', lugar);
            formData.append('hora', hora);
            formData.append('formato', formato);

            return fetch('../../api/SBinsertEvento.php', {
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
            return response.text();
        })
        .then(responseText => {
            buscarEvento();
            limpiar();
        })
        .catch(error => console.error('Error en altaEvento:', error))
        .finally(() => {
            popupAlta.style.display = 'none';
        });
}

function limpiar() {

    document.getElementById("nombre-popup").value = "";
    document.getElementById("fecha-popup").value = "";
    document.getElementById("descripcion-popup").value = "";
    document.getElementById("url-popup-alta").value = "";
    document.getElementById("lugar-popup").value = "";
    document.getElementById("hora-popup").value = "";
    document.getElementById("formato-popup").value = "";
}

function buscarEvento() {
    var name = document.getElementById("nombre").value;
    var fecha = document.getElementById("fecha").value;
    var descripcion = document.getElementById("descripcion").value;
    var lugar = document.getElementById("lugar").value;
    var formato = document.getElementById("formato").value;

    fetch('../../api/SBtokenProvider.php')
        .then(response => response.json())
        .then(data => {
            const token = data.token;
            const formData = new FormData();
            formData.append('name', name);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('lugar', lugar);
            formData.append('formato', formato);

            const url = "../../api/SBbuscarEvento.php";
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
            mostrarUsuario(data);
        })
        .catch(error => console.log('Error:', error));
}


function mostrarUsuario(usuarios) {
    var cuerpo = document.getElementById("cuerpo");
    cuerpo.innerHTML = "";
    resultado = document.getElementById("cuerpo");

    var tabla = usuarios;
    for (let i = 0; i < tabla.length; i++) {
        var tr = document.createElement("tr");

        var box = document.createElement("td");
        var check = document.createElement("input");
        check.type = "checkbox";
        box.append(check);
        tr.append(box);

        td1 = document.createElement("td");
        td1.innerHTML = tabla[i].Evento_id;
        td1.className = "c0";
        td2 = document.createElement("td");
        td2.innerHTML = tabla[i].Evento_nombre;
        td3 = document.createElement("td");
        td3.innerHTML = tabla[i].Evento_descripcion;
        td4 = document.createElement("td");
        td4.innerHTML = tabla[i].Evento_lugar;
        td5 = document.createElement("td");

        var fecha = new Date(tabla[i].Evento_fecha);
        var dia = ('0' + fecha.getDate()).slice(-2);
        var mes = ('0' + (fecha.getMonth() + 1)).slice(-2);
        var ano = fecha.getFullYear();
        td5.innerHTML = dia + '/' + mes + '/' + ano;

        td6 = document.createElement("td");
        var horaCompleta = tabla[i].Evento_hora.split(':');
        var horaFormateada = horaCompleta[0] + ':' + horaCompleta[1];
        td6.innerHTML = horaFormateada;

        td7 = document.createElement("td");
        td7.innerHTML = tabla[i].Evento_formato;
        td8 = document.createElement("td");
        td8.innerHTML = tabla[i].Evento_foto;

        tr.append(td1, td2, td3, td4, td5, td6, td7, td8);
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

                        return fetch('../../api/SBdeleteEvento.php', {
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
                buscarEvento();
                popupBaja.style.display = 'none';
            })
            .catch(error => console.error('Error al borrar eventos:', error));
    } else {
        alert("No se ha seleccionado ningún evento para eliminar.");
    }
}

function modificar() {
    var ids = getIds(); 
    if (ids.length !== 1) {
        alert("Por favor, seleccione un solo evento para modificar.");
        return;
    }
    var id = ids[0];
    var popupModificar = document.getElementById('popup-modificar');
    var name = document.getElementById("nombre-popupModf").value;
    var fecha = document.getElementById("fecha-popupModf").value;
    var descripcion = document.getElementById("descripcion-popupModf").value;
    var foto = document.getElementById("url-popupModf").value;
    var lugar = document.getElementById("lugar-popupModf").value;
    var hora = document.getElementById("hora-popupModf").value;
    var formato = document.getElementById("formato-popupModf").value;

    if (!validarFecha(fecha)) {
        alert('La fecha introducida no es válida. Por favor, introduzca una fecha en formato DD/MM/AAAA.');
        return;
    }

    if (!validarHora(hora)) {
        alert('La hora introducida no es válida. Por favor, introduzca una hora en formato HH:MM.');
        return;
    }

    fetch('../../api/SBtokenProvider.php')
        .then(response => response.json())
        .then(data => {
            const token = data.token;
            
            const formData = new FormData();
            formData.append('id', id);
            formData.append('name', name);
            formData.append('fecha', fecha);
            formData.append('descripcion', descripcion);
            formData.append('foto', foto);
            formData.append('lugar', lugar);
            formData.append('hora', hora);
            formData.append('formato', formato);

            return fetch('../../api/SBupdateEvento.php', {
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
            return response.text(); 
        })
        .then(responseText => {
            console.log("Respuesta del servidor:", responseText);
            buscarEvento(); 
            limpiar();  
        })
        .catch(error => console.error('Error en modificar:', error))
        .finally(() => {
            popupModificar.style.display = 'none'; 
        });
}


function validarFecha(fecha) {
    var formatoFecha = /^\d{4}-\d{2}-\d{2}$/;

    if (!fecha.match(formatoFecha)) {
        return false;
    }

    var partesFecha = fecha.split('-');
    var año = parseInt(partesFecha[0], 10);
    var mes = parseInt(partesFecha[1], 10) - 1;
    var dia = parseInt(partesFecha[2], 10);

    var fechaObj = new Date(año, mes, dia);
    fechaObj.setHours(0, 0, 0, 0);

    if (fechaObj.getFullYear() !== año || fechaObj.getMonth() !== mes || fechaObj.getDate() !== dia) {
        return false;
    } else {
        return true;
    }
}

function validarHora(hora) {
    var regexHora = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;

    return regexHora.test(hora);
}

function cancelar() {
    var checkboxes = document.querySelectorAll('#cuerpo input[type="checkbox"]');
    popupBaja.style.display = 'none';
    popupAlta.style.display = 'none';
    popupModificar.style.display = 'none';
    limpiar();

    checkboxes.forEach(function (checkbox) {
        checkbox.checked = false;
    });
    actualizarEstadoBotones();
}

function guardarValoresInputs() {
    sessionStorage.setItem('nombre', document.getElementById('nombre').value);
    sessionStorage.setItem('descripcion', document.getElementById('descripcion').value);
    sessionStorage.setItem('lugar', document.getElementById('lugar').value);
    sessionStorage.setItem('fecha', document.getElementById('fecha').value);
    sessionStorage.setItem('formato', document.getElementById('formato').value);
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