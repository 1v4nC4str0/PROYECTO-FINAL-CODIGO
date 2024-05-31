<?php

// Configuración de conexión a la base de datos remota
$server = "masesid256.mysql.db";
$database = "masesid256";
$username = "masesid256";
$password = "masESIC2024";


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



// Establecer la conexión a la base de datos remota
$conn = new mysqli($server, $username, $password, $database);
$conn->set_charset('utf8');

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el parámetro ID de manera segura
$id = $_POST["id"] ?? '';
$nombre = $_POST["nombre"] ?? '';

$query_parts = [];
$params = [];
$param_types = '';

if($id !== ''){
    $query_parts[] = 'Tipo_Actividad_ID LIKE ?';
    $params[] = "%{$id}%";
    $param_types .= 'i';
}

if($nombre !== ''){
    $query_parts[] = 'Tipo_Actividad_nombre LIKE ?';
    $params[] = "%{$nombre}%";
    $param_types .= 's';
}


// Consulta SQL con una consulta preparada
$sql = "SELECT Tipo_Actividad_ID, Tipo_Actividad_nombre FROM `masesic_Tipo_Actividad`";

if(!empty($query_parts)){
    $sql .= " WHERE " . implode(' AND ' , $query_parts);
}

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Vincular el parámetro y ejecutar la consulta

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

if ($param_types) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();

// Obtener el resultado de la consulta
$result = $stmt->get_result();

// Crear un array para almacenar los resultados
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Establecer la cabecera de la respuesta como JSON
header('content-type: application/json');

// Devolver los datos en formato JSON
echo json_encode($data);

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

