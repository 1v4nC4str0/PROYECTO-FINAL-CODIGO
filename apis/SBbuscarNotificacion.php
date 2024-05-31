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
    $response = ['info' => 'Error - Solicitud no autorizada'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$notificacion_texto = $_POST["descripcion"] ?? '';
$notificacion_tipo = $_POST["tipo"] ?? '';
if (isset($_POST['fecha_rango'])) {
    $fechas = preg_split('/ \s*a\s*|\s*to\s* /i', $_POST['fecha_rango']); 
    $fecha_inicio = $fechas[0];
    $fecha_fin = isset($fechas[1]) ? $fechas[1] : $fechas[0];
} else {
    $fecha_inicio = '';
    $fecha_fin = '';
}

$query_parts = [];
$params = [];
$param_types = '';

if ($notificacion_texto !== '') {
    $query_parts[] = "N.notificacion_texto LIKE ?";
    $params[] = "%{$notificacion_texto}%";
    $param_types .= 's';
}
if ($notificacion_tipo !== '') {
    $query_parts[] = "N.notificacion_tipo LIKE ?";
    $params[] = "%{$notificacion_tipo}%";
    $param_types .= 's';
}
if ($fecha_inicio !== '' && $fecha_fin !== '') {
    $fecha_fin_adjusted = $fecha_fin . ' 23:59:59';
    $query_parts[] = "(N.notificacion_creacion BETWEEN ? AND ? OR N.notificacion_creacion LIKE ?)";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin_adjusted;
    $params[] = $fecha_fin . '%'; 
    $param_types .= 'sss';
}

$sql = "SELECT N.*, CASE 
            WHEN N.notificacion_tipo = 'actividad' THEN A.Actividad_nombre 
            WHEN N.notificacion_tipo = 'evento' THEN E.Evento_nombre 
        END AS nombre_referencia 
        FROM masesic_notificacion N 
        LEFT JOIN masesic_Notificacion_actividad NA ON N.notificacion_id = NA.notificacion_id 
        LEFT JOIN masesic_Actividad A ON NA.actividad_ID = A.Actividad_ID 
        LEFT JOIN masesic_Notificacion_evento NE ON N.notificacion_id = NE.notificacion_id 
        LEFT JOIN masesic_Evento E ON NE.evento_id = E.Evento_id";

if (!empty($query_parts)) {
    $sql .= " WHERE " . implode(' AND ', $query_parts);
}
$sql .= " ORDER BY N.notificacion_creacion DESC";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

if (!empty($param_types)) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
// echo json_encode('params: '.$fecha_inicio);
$stmt->close();
$conn->close();
?>
