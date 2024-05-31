<?php

$config = require('config.php');
$expectedToken = $config['token'];

$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
} else if (isset($headers['Token'])) {
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
    die("ERROR" . $conn->connect_error);
}

$sql = "SELECT Usuario_id, Usuario_Nombre, Usuario_Apellidos, Usuario_Telefono, Usuario_Mail, Grado_id FROM `masesic_Usuario` WHERE 1 = 1";
$types = "";
$params = [];

// Recolectar parámetros
if (isset($_REQUEST['Usuario_Nombre']) && $_REQUEST['Usuario_Nombre'] != "") {
    $sql .= " AND Usuario_Nombre = ?";
    $types .= "s";
    $params[] = $_REQUEST['Usuario_Nombre'];
}
if (isset($_REQUEST['Usuario_Apellidos']) && $_REQUEST['Usuario_Apellidos'] != "") {
    $sql .= " AND Usuario_Apellidos = ?";
    $types .= "s";
    $params[] = $_REQUEST['Usuario_Apellidos'];
}
if (isset($_REQUEST['Usuario_Telefono']) && $_REQUEST['Usuario_Telefono'] != "") {
    $sql .= " AND Usuario_Telefono = ?";
    $types .= "s";
    $params[] = $_REQUEST['Usuario_Telefono'];
}
if (isset($_REQUEST['Usuario_Mail']) && $_REQUEST['Usuario_Mail'] != "") {
    $sql .= " AND Usuario_Mail = ?";
    $types .= "s";
    $params[] = $_REQUEST['Usuario_Mail'];
}
if (isset($_REQUEST['Grado_id']) && $_REQUEST['Grado_id'] != "") {
    $sql .= " AND Grado_id = ?";
    $types .= "i";
    $params[] = $_REQUEST['Grado_id']; // Asumiendo que Grado_id es un entero
}

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular parámetros
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

// Crear un array para almacenar los resultados
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

?>