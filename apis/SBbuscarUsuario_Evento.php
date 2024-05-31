<?php
$servername = "masesid256.mysql.db";
$username = "masesid256";
$password = "masESIC2024";
$dbname = "masesid256";


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



$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

$evento_id = $_POST["evento_id"];
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT ue.Evento_id, e.Evento_nombre, ue.usuario_id, u.Usuario_Nombre , u.Usuario_Apellidos, e.Evento_fecha FROM masesic_Evento_Usuario AS ue JOIN masesic_Usuario u ON ue.usuario_id = u.Usuario_id JOIN masesic_Evento e ON ue.Evento_id = e.Evento_id WHERE ue.Evento_id = ?");

$stmt->bind_param("i", $evento_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(array('message' => "No se encontró el registro con ID $evento_id."));
    }
} else {
    echo json_encode(array('error' => "ERROR: No se pudo ejecutar la consulta. " . $stmt->error));
}

$stmt->close();
$conn->close();
