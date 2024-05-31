
<?php


function desencriptarTelefono($telefonoEncriptado) {
     $claveSecreta = 123897192;
     $telefonoEncriptado = (int) $telefonoEncriptado;
     $telefono = $telefonoEncriptado ^ $claveSecreta; // XOR de nuevo con la clave secreta
     return (string) $telefono;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);   
     $asunto = "masEsic";
     $telefono = "+34" . desencriptarTelefono($_REQUEST['telefono']);
     $texto = $_REQUEST["texto"];

     require_once(__DIR__ . '/Instasent/src/Instasent/Abstracts/InstasentClient.php');
     require_once(__DIR__ . '/Instasent/src/Instasent/SmsClient.php');
     
     $instasentClient = new Instasent\SmsClient("8889a539b6d911e78a70d7231255b1ca92975003");
     $response = $instasentClient->sendUnicodeSms($asunto, $telefono, $texto);
     
     echo $response["response_code"];
     echo $response["response_body"];     
?>