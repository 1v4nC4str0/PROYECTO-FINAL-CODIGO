<?php

$config = require('config.php');
$expectedToken = $config['token'];




$headers = apache_request_headers();
$token = null;
if(isset($headers['token'])){
    $token = $headers['token'] ;
} else if(isset($headers['Token'])){
    $token = $headers['Token'] ;
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
    die("Error de conexión: " . $connexion->connect_error);
}

$tipo_actividad_nombre = $_POST["name"];

$sql = "INSERT INTO masesic_Tipo_Actividad(Tipo_Actividad_nombre) VALUES (?)";

$stmt = $connexion->prepare($sql);

// Vincular los parámetros y ejecutar la consulta
$stmt->bind_param("s", $tipo_actividad_nombre);

if ($stmt->execute()) {
    echo "El registro ha sido insertado correctamente.";
} else {
    echo "Error al insertar el registro: " . $stmt->error;
}

// Verificar si la consulta se ejecutó con éxito

// Cerrar la conexión y liberar los recursos
$stmt->close();
$connexion->close();
?>