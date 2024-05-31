<?php
    function desencriptarTelefono($telefonoEncriptado) {
        $claveSecreta = 123897192;
        $telefonoEncriptado = (int) $telefonoEncriptado;
        $telefono = $telefonoEncriptado ^ $claveSecreta; // XOR de nuevo con la clave secreta
        return (string) $telefono;
   }
    require_once 'SconsultaEventos.php';

    $connexion = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
    $connexion->set_charset('utf8');
    
        $sql = "SELECT  evento_id, evento_nombre, evento_fecha, evento_descripcion, evento_foto, evento_lugar, evento_hora, evento_formato
        FROM masesic_Evento order by evento_fecha DESC";
        $stmt = $connexion->prepare($sql);
    




    $stmt->execute();

    $result=$stmt->get_result();
    $data = array();
    while($row=$result->fetch_assoc()){
        $data[]=$row;
    }
    
    for ($i=0; $i < count($result); $i++) { 
        $fecha_base_datos = $result[$i]['Evento_fecha']
        $fecha_obj = new DateTime($fecha_base_datos);
        $fecha_actual = new DateTime('now');
        $intervalo = $fecha_actual->diff($fecha_obj);
    
        // Verificar si faltan  2 días
        if ($intervalo->days > 1 && $intervalo->days <= 2 && $fecha_actual < $fecha_obj) {


            $connexion = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
            $connexion->set_charset('utf8');
            
                $sql = "SELECT
                masesic_Usuario.Usuario_Nombre,
                masesic_Usuario.Usuario_Apellidos,
                masesic_Usuario.Usuario_Telefono,
                curso.nombre
              FROM masesic_Usuario
              JOIN masesic_Evento_Usuario
                ON masesic_Usuario.Usuario_id = masesic_Evento_Usuario.usuario_id
              JOIN masesic_Evento
                ON masesic_Evento.Evento_id = masesic_Evento_Usuario.Evento_id WHERE masesic_Evento = ?;";
                $stmt = $connexion->prepare($sql);
            

                $stmt->bind_param("s", $result[$i]['Evento_id']);


            $stmt->execute();

            $result2=$stmt->get_result();
            $data = array();
            while($row=$result2->fetch_assoc()){
                $data[]=$row;
            }

                for ($j=0; $j < count($result2); $j++) { 
                    $asunto = "masEsic";
                    $telefono = "+34".desencriptarTelefono($_REQUEST['telefono']);

                    $fecha_bd = $result[$i]['Evento_fecha']; 
    
                    $fecha_objeto = new DateTime($fecha_base_datos);
                    
                    $fecha_formateada = $fecha_obj->format('d/m/Y');

                    $plantilla = file_get_contents('plantillaCorreo/plantilla.txt');
                    $plantilla = str_replace('{{FECHA}}', $fecha_formateada, $plantilla);
                    $plantilla = str_replace('{{HORA}}', $result[$i]['Evento_hora'], $plantilla);
                    $plantilla = str_replace('{{EVENTO}}', $result[$i]['Evento_nombre'], $plantilla);
                    $plantilla = str_replace('{{LOCALIZACION}}', $result[$i]['Evento_lugar'], $plantilla);
                    

                    $texto = $plantilla;

                    require_once(__DIR__ . '/Instasent/src/Instasent/Abstracts/InstasentClient.php');
                    require_once(__DIR__ . '/Instasent/src/Instasent/SmsClient.php');
                    
                    $instasentClient = new Instasent\SmsClient("8889a539b6d911e78a70d7231255b1ca92975003");
                    $response = $instasentClient->sendUnicodeSms($asunto, $telefono, $texto);
                    
                    echo $response["response_code"];
                    echo $response["response_body"];   
                }
        }
    }





?>