<?php
$id_evento = $_POST["evento_id"];
$id_usuario = $_POST["usuario_id"];


$config = require('config.php');
$expectedToken = $config['token'];


$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
} elseif (isset($headers['Token'])){
    $token = $headers['Token'];
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

if ($connexion->connect_error) {
    die("ERROR". $connect_error);
}

$sql = "DELETE FROM masesic_Evento_Usuario WHERE Evento_id = ? AND Usuario_id = ?";

$stmt = $connexion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ii", $id_evento, $id_usuario); 

    if ($stmt->execute()) {
        echo "El registro del usuario $id_usuario en el evento $id_evento se ha eliminado correctamente.";
    } else {
        echo "Error al eliminar el registro: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error al preparar la consulta: " . $connexion->error;
}

$connexion->close();
