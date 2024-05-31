<?php

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

// Conexión a la base de datos
$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$conn->set_charset('utf8');

// Verificar si hubo un error en la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Parámetros recibidos por POST
$grado_id = isset($_POST["grado_id"]) ? $_POST["grado_id"] : null;
$grado_nombre = isset($_POST["grado_Nombre"]) ? $_POST["grado_Nombre"] : null;
$tipo_grado_id = isset($_POST["tipo_grado_Id"]) ? $_POST["tipo_grado_Id"] : null;

// Consulta SQL base
$sql = "SELECT grado_id, grado_Nombre, tipo_grado_Id FROM `masesic_Grado`";

// Array para condiciones y parámetros
$conditions = array();
$params = array();

// Construir la consulta dinámicamente
if (!empty($grado_id)) {
    $conditions[] = "grado_id LIKE ?";
    $params[] = "%$grado_id%";
}
if (!empty($grado_nombre)) {
    $conditions[] = "grado_Nombre LIKE ?";
    $params[] = "%$grado_nombre%";
}
if (!empty($tipo_grado_id)) {
    $conditions[] = "tipo_grado_Id LIKE ?";
    $params[] = "%$tipo_grado_id%";
}

// Agregar condiciones a la consulta si existen
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la sentencia tuvo éxito
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Enlazar los parámetros con los marcadores de posición
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

// Ejecutar la consulta
$stmt->execute();

// Obtener el resultado de la consulta
$result = $stmt->get_result();

// Crear un array para almacenar los resultados
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión y la sentencia
$stmt->close();
$conn->close();
?>