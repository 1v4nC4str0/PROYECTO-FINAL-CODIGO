<?php


$config = require('config.php');
$expectedToken = $config['token'];


$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
} else if(isset($headers['Token'])){
    $token = $headers['Token'];
}


if ($token !== $expectedToken) {
    http_response_code(401);
    $response['info'] = 'Error - Solicitud no autorizada';
    $encoded = json_encode($response); 
    header('Content-type: application/json');
    exit($encoded);
}



$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$conn->set_charset('utf8');

if ($conn->connect_error) {
    die("ERROR: " . $conn->connect_error);
}

if (isset($_POST["Usuario_id"])) {

    $Usuario_id = $_POST["Usuario_id"];

    $sql = "DELETE FROM masesic_Usuario WHERE Usuario_id=?";
   
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error al realizar la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $Usuario_id);
    
    if ($stmt->execute()) {
        echo "Se ha ejecutado correctamente la baja del usuario con ID: $Usuario_id";
    } else {
        die("Error al realizar la baja del usuario: " . $conn->error);
    }
    
    $stmt->close();
} else {
    die("No se proporcionó el ID del usuario para realizar la baja.");
}

$conn->close();
?>


