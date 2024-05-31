<?php
$config = require('config.php');
$expectedToken = $config['token'];

// Obtener el token de la cabecera de autorización
$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
} else if(isset($headers['Token'])) {
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

$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$conn->set_charset('utf8');

if ($conn->connect_error) {
    die("ERROR: " . $conn->connect_error);
}

if (isset($_POST["Usuario_id"]) && isset($_POST["Usuario_Nombre"]) && isset($_POST["Usuario_Apellidos"]) && isset($_POST["Usuario_Telefono"]) && isset($_POST["Usuario_Mail"]) && isset($_POST["Grado_id"])) {

    $Usuario_id = $_POST["Usuario_id"];
    $Usuario_Nombre = $_POST["Usuario_Nombre"];
    $Usuario_Apellidos = $_POST["Usuario_Apellidos"];
    $Usuario_Telefono = $_POST["Usuario_Telefono"];
    $Usuario_Mail = $_POST["Usuario_Mail"];
    $Grado_id = $_POST["Grado_id"];
    
    $sql = "UPDATE masesic_Usuario SET Usuario_Nombre=?, Usuario_Apellidos=?, Usuario_Telefono=?, Usuario_Mail=?, Grado_id=? WHERE Usuario_id=?";
   
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("ssssii", $Usuario_Nombre, $Usuario_Apellidos, $Usuario_Telefono, $Usuario_Mail, $Grado_id, $Usuario_id);
    
    if ($stmt->execute()) {
        echo "Se han actualizado correctamente los datos del usuario con ID: $Usuario_id";
    } else {
        die("Error al actualizar los datos del usuario: " . $conn->error);
    }
    
    $stmt->close();
} else {
    die("No se proporcionaron todos los datos necesarios para actualizar el usuario.");
}

$conn->close();
?>
