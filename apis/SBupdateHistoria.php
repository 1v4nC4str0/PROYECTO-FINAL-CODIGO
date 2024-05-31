<?php
$config = require('config.php');
$expectedToken = $config['token'];
$id=$_POST["id"];
$name=$_POST["name"];
$desc=$_POST["desc"];
$foto=$_POST["image"];
$actID=$_POST["actid"];

// Obtener el token de la cabecera de autorización
$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
}else if (isset($headers['Token'])) {
    $token = $headers['Token'];
}

// comparar token
//$expectedToken = '12345';
if ($token !== $expectedToken) {
    http_response_code(401);
    $response['info'] = 'Error - Solicitud no autorizada';
    $encoded = json_encode($response); 
    header('Content-type: application/json');
    exit($encoded);
}

$connexion = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$connexion->set_charset('utf8');

if($connexion->connect_error){
    die("ERROR".$connexion->connect_error);
}
$sql = "UPDATE masesic_Historias SET historia_nombre=?, historia_descripcion=?, historia_foto=?, actividad_id=? WHERE historia_id = ?";
$stmt = $connexion->prepare($sql);
$stmt->bind_param("sssii",$name,$desc,$foto,$actID,$id);

if($stmt->execute()){
    echo "El registro con id $id se ha modificado con exito";
}else{
    echo "Error update".$connexion->error;
}
$connexion->close();

?>