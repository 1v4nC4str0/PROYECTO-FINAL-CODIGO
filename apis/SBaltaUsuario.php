<?php
$config = require('config.php');
$expectedToken = $config['token'];

header("Content-Type: application/json; charset=UTF-8");

$usuario_nombre = $_POST["usuario_nombre"];
$usuario_apellidos = $_POST["usuario_apellidos"];
$usuario_telefono = $_POST["usuario_telefono"];
$usuario_mail = $_POST["usuario_mail"];
$grado_id = $_POST["grado_id"];



$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
} else if(isset($headers['Token'])){
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

if ($connexion->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection error: " . $connexion->connect_error]);
    exit;
}

$sql = "INSERT INTO masesic_Usuario(Usuario_Nombre,Usuario_Apellidos, Usuario_Telefono, Usuario_Mail, Grado_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $connexion->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Error al preparar la consulta: " . $connexion->error]);
    exit;
}

$stmt->bind_param("sssss", $usuario_nombre, $usuario_apellidos, $usuario_telefono, $usuario_mail, $grado_id);
$executed = $stmt->execute();

if ($executed) {
    echo json_encode(["message" => "El registro del usuario telf: $usuario_telefono se ha insertado correctamente"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error de inserción: " . $stmt->error]);
}

$stmt->close();
$connexion->close();
?>