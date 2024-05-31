<?php
$config = require('config.php');
$expectedToken = $config['token'];
$grado_Nombre = urldecode($_POST["grado_Nombre"]);
$tipo_grado_Id = urldecode($_POST["tipo_grado_Id"]);
$grado_id = $_POST["grado_id"];

// Obtener el token de la cabecera de autorización
$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
}else if (isset($headers['Token'])) {
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

// Conexión a la base de datos
$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
 
// Verificar si hubo un error en la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}


$sql = "UPDATE masesic_Grado SET grado_Nombre=?, tipo_grado_Id=? WHERE grado_id = ?";


$stmt = $conn->prepare($sql);


if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}


$stmt->bind_param("ssi", $grado_Nombre, $tipo_grado_Id, $grado_id);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "El registro con ID $grado_id ha sido modificado correctamente.";
} else {
    echo "Error al modificar el registro: " . $stmt->error;
}

// Cerrar la sentencia y la conexión
$stmt->close();
$conn->close();
?>
