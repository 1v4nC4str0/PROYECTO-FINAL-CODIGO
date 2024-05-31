<?php
$config = require('config.php');
$expectedToken = $config['token'];

$servername = "masesid256.mysql.db";
$username = "masesid256";
$password = "masESIC2024";
$dbname = "masesid256";

// Obtener los parámetros de manera segura
    $id_act =$_POST["id"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $foto =  $_POST["foto"];
    $tipoid =  $_POST["tipactid"];


// Obtener el token de la cabecera de autorización
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

// Crear una nueva conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
// Establecer el conjunto de caracteres UTF-8
$conn->set_charset("utf8");

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}



    // Consulta SQL con una consulta preparada para actualizar el registro en la tabla masesic_Actividad
    $sql = "UPDATE masesic_Actividad SET Actividad_nombre = ?, Actividad_descripcion = ?, Actividad_foto = ?, tipo_actividad_id = ?  WHERE Actividad_ID = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    
    // Verificar si hubo un error al preparar la consulta
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular los parámetros y ejecutar la consulta
    $stmt->bind_param("sssii", $nombre, $descripcion, $foto, $tipoid,  $id_act);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "El registro con ID $id ha sido modificado correctamente.";
        echo json_encode($response);
    } else {
        $response['error'] = 'Error al ejecutar la consulta: ' . $stmt->error;
        http_response_code(500);
        echo json_encode($response);
    }


// Cerrar la consulta preparada
    $stmt->close();
// Cerrar la conexión
    $conn->close();
?>
