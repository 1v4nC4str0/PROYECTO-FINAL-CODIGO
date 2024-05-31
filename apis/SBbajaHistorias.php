<?php
$id=$_POST["id"];


$config = require('config.php');
$expectedToken = $config['token'];


$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
}

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
$sql = "DELETE FROM masesic_Historias WHERE historia_id = ?";
$stmt = $connexion->prepare($sql);
$stmt->bind_param("i",$id);

if($stmt->execute()){
    echo "El registro con id $id se ha borrado con exito";
}else{
    echo "Error inserción".$connexion->error;
}
$stmt->close();
$connexion->close();

?>