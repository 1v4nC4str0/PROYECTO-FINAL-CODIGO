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
$actividad_nombre = $_POST["nombre"] ?? '';
$actividad_descripcion = $_POST["descripcion"] ?? '';
$tipo_nombre = $_POST["tipo_act_ent"] ?? '';

$query_parts = [];
$params = [];
$param_types = '';

if($actividad_nombre !== ''){
    $query_parts[] = 'Actividad_nombre LIKE ?';
    $params[] = "%{$actividad_nombre}%";
    $param_types .= 's';
}
if($actividad_descripcion !== ''){
    $query_parts[] = 'Actividad_descripcion LIKE ?';
    $params[] = "%{$actividad_descripcion}%";
    $param_types .= 's';
}
if($tipo_nombre !== ''){
    $query_parts[] = 'a.Tipo_Actividad_ID = ?';
    $params[] = $tipo_nombre;
    $param_types .= 'i';
}


// Consulta SQL con una consulta preparada
// $sql = "SELECT Actividad_ID, Actividad_nombre, Actividad_descripcion, Actividad_foto, tipo_actividad_id 
//         FROM `masesic_Actividad`";

$sql = "SELECT a.Actividad_ID, a.Actividad_nombre, a.Actividad_descripcion, a.Actividad_foto, ta.Tipo_Actividad_nombre, a.tipo_actividad_id
FROM `masesic_Actividad` a 
JOIN `masesic_Tipo_Actividad` ta ON a.tipo_actividad_id = ta.Tipo_Actividad_ID";


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

if ($result->num_rows === 0) {
    // No se encontraron resultados, enviar un mensaje de error
    http_response_code(404);
    $response['error'] = 'No se encontraron resultados.';
    echo json_encode($response);
    exit;
}


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
