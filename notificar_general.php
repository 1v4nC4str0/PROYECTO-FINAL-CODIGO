<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Notificar</title>
    <link rel="stylesheet" href="view/css/abm.css?v=<?=rand(0,9999999)?>">
    <link rel="stylesheet" href="view/css/noti.css">
    <style>
        #alerta {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            z-index: 1000;
        }

        /* Fondo para oscurecer la pantalla */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>

<body>
    <div class="contenedor-gral">
        <div class="banner">
            <div class="logo">
                <img src="view/img/logomasEsic.png" alt="">
            </div>
            <div class="espacio"></div>
            <div class="botones-inicio-notificar">
                <a href="index.html"><button class="boton-banner">INICIO</button></a>
                
            </div>
        </div>
        <div class="elemento-arriba">
            <div class="componente"></div>
            <div class="rectangulo-vertical"></div>
        </div>
        
        <div class="contenedor-notif">
            <div class="contenedor-form" id="contenedor-form">
                <form class="form-notif">
                    <div class="notificacion-listas">
                        <div id="quienes" class="mb">
                            <label class="mb txt" for="quienes-select">A quienes se envia la notificacion:</label>
                            <select name="quienes-notificacion" class="txt entrada" id="quienes-select">
                                <option class="txt" value="todos" selected>Todos los usuarios</option>
                                <option class="txt" value="actividad">Usuarios que pertenecen a una actividad</option>
                                <option class="txt" value="evento">Usuarios que pertenecen a un evento</option>
                            </select>
                        </div>
                        <div id="quienes-especifico" class="noSeVe mb">
                            <label class="mb txt" for="quienes-especifico-select" >Cuál en especifico:</label>
                            <select name="quienes-especifico-select" class="entrada" id="quienes-especifico-select">
                                
                            </select>
                        </div>
                    </div>
                    <label class="mb txt" for="titulo-notificacion" hidden>¿Qué asunto tendrá la notificación?</label>
                    <input type="text" class="inputs mb entrada" name="titulo-notificacion" id="titulo-notificacion" hidden>
                    <label class="mb txt" for="texto-notificacion">¿Qué mensaje quieres enviar?</label>
                    <textarea class="inputs-xl mb-2 entrada" name="titulo-notificacion" id="texto-notificacion" placeholder="Escriba el mensaje aquí..."></textarea>

                    
                </form>
                <button class="botones btn" id="boton-enviar">
                        Enviar
                    </button>
            </div>
        </div>
        
        <div class="elemento-abajo" id="doss">
            <div class="componente" id="dos"></div>
            <div class="rectangulo-vertical"></div>
        </div>
    </div>
    <div id="overlay"></div>
    <div id="alerta" style="display:none;">
        <p>Mensaje enviado</p>
    </div>

    <script>
        function showPopup() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('alerta').style.display = 'block';

            setTimeout(() => {
                window.location.href="https://masesic.org/admin"
            }, 1000);
        }
    </script>

    <script>
    var formulario = document.getElementById("contenedor-form");
    var quienesSelect = document.getElementById("quienes-select");
    var quienesEspecifico = document.getElementById("quienes-especifico");
    var botonEnviar = document.getElementById("boton-enviar");

    
    botonEnviar.addEventListener("click", envio);

    function envio(e){
        e.preventDefault();
        //telefono de luengo = 633200230
        if(quienesSelect.value=="todos"){

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Token': 'Bffujnkjnxczko7w3udbDSFFDjineg48hw34Wwne78DBXmAWWsdfnidsfogejniohoiu'
                }
            };

            fetch('https://masesic.org/api/SConsultaUsuario.php', options)

                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    enviarSms(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else if (quienesSelect.value=="evento") {
            var evento_id = document.getElementById("quienes-especifico-select").value;
            console.log(evento_id);
            const data = {
                evento_id: evento_id
            };

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Token': 'Bffujnkjnxczko7w3udbDSFFDjineg48hw34Wwne78DBXmAWWsdfnidsfogejniohoiu'
                },
                body: 'evento_id=' + encodeURIComponent(evento_id)
            };

            fetch('https://masesic.org/api/SconsultaUsuarios_Evento_porEvento.php', options)

                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    enviarSms(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else if (quienesSelect.value=="actividad") {
            var actividad_id = document.getElementById("quienes-especifico-select").value;
            console.log(actividad_id);
            const data = {
                actividad_id: actividad_id
            };

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Token': 'Bffujnkjnxczko7w3udbDSFFDjineg48hw34Wwne78DBXmAWWsdfnidsfogejniohoiu'
                },
                body: 'actividad_id=' + encodeURIComponent(actividad_id)
            };

            fetch('https://masesic.org/api/SconsultaUsuarios_Actividad_porActividad.php', options)

                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    enviarSms(data);
 
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }

    

    function enviarSms(datos) {
        var asunto = document.getElementById("titulo-notificacion").value;
        var texto = document.getElementById("texto-notificacion").value;
        for (let i = 0; i < datos.length; i++) {
            const data = {
                asunto: asunto,
                texto: texto,
                telefono: datos[i].Usuario_Telefono
            };

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Token': 'Bffujnkjnxczko7w3udbDSFFDjineg48hw34Wwne78DBXmAWWsdfnidsfogejniohoiu'
                },
                body: 'asunto=' + encodeURIComponent(asunto) + '&texto=' + encodeURIComponent(texto) + '&telefono=' + encodeURIComponent(datos[i].Usuario_Telefono)
            };

            fetch('https://masesic.org/api/notificacion_accion.php', options)

                .then(  
                      
                    showPopup()
                )
                
                .catch(error => {
                    console.error('Error:', error);
                });
            
        }
    }

    quienesSelect.addEventListener("change", cambio);


    function cambio(e){
        
        if(e.target.value!="todos"){
            if (e.target.value=="evento") {
                cargarLosEspecificos("evento");
            } else if (e.target.value=="actividad") {
                cargarLosEspecificos("actividad");
            }
            quienesEspecifico.classList.remove("noSeVe");
        } else {
            quienesEspecifico.classList.add("noSeVe");
        }
    }

    function cargarLosEspecificos(t){
        if (t=="actividad") {
            
            const data = {
                 1: 1
            };

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Token': 'Bffujnkjnxczko7w3udbDSFFDjineg48hw34Wwne78DBXmAWWsdfnidsfogejniohoiu'
                }
            };

            fetch('https://masesic.org/api/SconsultaActividad.php', options)

                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    fabricarOptions(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else if(t=="evento") {
            const data = {
                 1: 1
            };

            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Token': 'Bffujnkjnxczko7w3udbDSFFDjineg48hw34Wwne78DBXmAWWsdfnidsfogejniohoiu'
                }
            };

            fetch('https://masesic.org/api/SbuscarEvento.php', options)

                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    fabricarOptions(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }

    function alta(texto, tipo){
            

            fetch('../../api/tokenProvider.php')
                .then(response => response.json())
                .then(data => {
                    const token = data.token;
                    const formData = new FormData();
                
                        formData.append('notificacion_texto', texto);
                        formData.append('notificacion_tipo', tipo);
                    
                    const url = "../../api/SBaltaNotificacion.php";
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
                    
                    
                })
                .catch(error => console.log('Error:', error));
        }

    function fabricarOptions(data) {
        var quienesEspecificoSelect = document.getElementById("quienes-especifico-select");
        quienesEspecificoSelect.innerHTML = "";
        console.log(data);

        if (quienesSelect.value=="evento") {
            for (let i = 0; i < data.length; i++) {
            option = document.createElement("option");
            option.innerHTML= data[i].Evento_nombre;
            option.value= data[i].Evento_id;
            quienesEspecificoSelect.append(option);
        }
        }else if (quienesSelect.value=="actividad") {
            for (let j = 0; j < data.length; j++) {
            option = document.createElement("option");
            option.innerHTML= data[j].Actividad_nombre;
            option.value= data[j].Actividad_ID;
            quienesEspecificoSelect.append(option);
        }
        }
    }
    
</script>

</body>



</html>


