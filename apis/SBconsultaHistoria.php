<?php

$id = $_REQUEST["id"];
$nombre = $_REQUEST["historia_nombre"];
$descripcion = $_REQUEST["historia_descripcion"];
$actividad = $_REQUEST["actividad_id"];

$config = require('config.php');
$expectedToken = $config['token'];

$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
}else if (isset($headers['Token'])) {
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
$sql = "SELECT historia_id, historia_nombre, historia_descripcion, historia_foto, actividad_id FROM masesic_Historias";

$conditions = array();
$params = array();

if (isset($id)) {
    $conditions[] = "historia_id LIKE ?";
    $params[] = "%$id%";
}
if (isset($nombre)) {
    $conditions[] = "historia_nombre LIKE ?";
    $params[] = "%$nombre%";
}
if (isset($descripcion)) {
    $conditions[] = "historia_descripcion LIKE ?";
    $params[] = "%$descripcion%";
}
if (isset($actividad)) {
    $conditions[] = "actividad_id LIKE ?";
    $params[] = "%$actividad%";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY historia_id DESC";

$stmt = $connexion->prepare($sql);

if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('content-type: application/json');
echo json_encode($data);

?>