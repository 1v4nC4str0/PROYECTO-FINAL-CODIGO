<?php

$config = require('config.php');
$expectedToken = $config['token'];


$
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


$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$conn->set_charset("utf8");

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los parámetros de manera segura
$name = $_POST["name"];
$desc = $_POST["descripcion"];
$foto = $_POST["foto"];
$tipactID = $_POST["tipactid"];


// Consulta SQL con una consulta preparada para insertar la actividad
$sql = "INSERT INTO masesic_Actividad (Actividad_nombre, Actividad_descripcion, Actividad_foto, tipo_actividad_id) VALUES (?, ?, ?, ?)";

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Vincular los parámetros y ejecutar la consulta
$stmt->bind_param("sssi", $name, $desc, $foto, $tipactID);
if ($stmt->execute()) {
    echo "El registro ha sido insertado correctamente.";
} else {
    echo "Error al insertar el registro: " . $stmt->error;
}

// Verificar si la consulta se ejecutó con éxito
if ($stmt->affected_rows > 0) {
    echo "El registro con nombre $name se ha insertado con éxito";
} else {
    echo "Error al insertar el registro: " . $conn->error;
}

// Cerrar la conexión y liberar los recursos
$stmt->close();
$conn->close();
?>