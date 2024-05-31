<?php
$server = "masesid256.mysql.db";
$database = "masesid256";
$username = "masesid256";
$password = "masESIC2024";

// API de consultaD
$config = require ('config.php');
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


$conn = new mysqli($server, $username, $password, $database);
$conn->set_charset('utf8');


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$actividad_id = $_REQUEST['actividad_id'];

if (is_null($actividad_id)) {
    http_response_code(400);
    $response = ['error' => 'Faltan parámetros requeridos: actividad_id'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


$sql ="SELECT ua.usuario_id, u.Usuario_Nombre, u.Usuario_Apellidos, ua.actividad_ID, a.Actividad_nombre 
                            FROM masesic_Usuario_Actividad AS ua 
                            JOIN masesic_Usuario AS u ON ua.usuario_id = u.Usuario_id 
                            JOIN masesic_Actividad AS a ON ua.actividad_ID = a.Actividad_ID  
                            WHERE ua.actividad_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $actividad_id);
$stmt->execute();
$result = $stmt->get_result();

// Crear un array para almacenar los resultados
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
// Establecer la cabecera de la respuesta como JSON
header('content-type: application/json');

echo json_encode($data);

// Cerrar la conexión
$stmt->close();
$conn->close();
