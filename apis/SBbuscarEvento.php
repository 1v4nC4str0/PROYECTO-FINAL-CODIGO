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

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$evento_nombre = $_POST["name"] ?? '';
$evento_fecha = $_POST["fecha"] ?? '';
$evento_descripcion = $_POST["descripcion"] ?? '';
$evento_lugar = $_POST["lugar"] ?? '';
$evento_hora = $_POST["hora"] ?? '';
$evento_formato = $_POST["formato"] ?? '';

$query_parts = [];
$params = [];
$param_types = '';

if ($evento_nombre !== '') {
    $query_parts[] = "Evento_nombre LIKE ?";
    $params[] = "%{$evento_nombre}%";
    $param_types .= 's';
}
if ($evento_fecha !== '') {
    $query_parts[] = "Evento_fecha LIKE ?";
    $params[] = "%{$evento_fecha}%";
    $param_types .= 's';
}
if ($evento_descripcion !== '') {
    $query_parts[] = "Evento_descripcion LIKE ?";
    $params[] = "%{$evento_descripcion}%";
    $param_types .= 's';
}
if ($evento_lugar !== '') {
    $query_parts[] = "Evento_lugar LIKE ?";
    $params[] = "%{$evento_lugar}%";
    $param_types .= 's';
}
if ($evento_hora !== '') {
    $query_parts[] = "Evento_hora LIKE ?";
    $params[] = "%{$evento_hora}%";
    $param_types .= 's';
}
if ($evento_formato !== '') {
    $query_parts[] = "Evento_formato LIKE ?";
    $params[] = "%{$evento_formato}%";
    $param_types .= 's';
}

$sql = "SELECT Evento_id, Evento_nombre, Evento_fecha, Evento_descripcion, Evento_foto, Evento_lugar, Evento_hora, Evento_formato FROM `masesic_Evento`";
if (!empty($query_parts)) {
    $sql .= " WHERE " . implode(' AND ', $query_parts);
}

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

if ($param_types) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>