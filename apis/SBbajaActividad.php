<?php
$config = require('config.php');
$expectedToken = $config['token'];

$headers = apache_request_headers();
$token = null;
if(isset($headers['token'])){
    $token = $headers['token'] ;
} else if(isset($headers['Token'])){
    $token =$headers['Token'] ;
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

$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$conn->set_charset("utf8");
// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
if(isset($_POST["id"])) {
// Obtener el parámetro ID de manera segura
$id = $_POST["id"];


// Consulta SQL con una consulta preparada para eliminar la actividad
$sql = "DELETE FROM masesic_Actividad WHERE Actividad_ID = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Vincular el parámetro y ejecutar la consulta
$stmt->bind_param("s", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        echo "El registro ha sido eliminado correctamente.";
    } else {
        echo "No se encontró ningún registro que coincida con los criterios especificados.";
    }
} else {
    echo "Error al ejecutar la consulta: " . $stmt->error;
}
}

// Cerrar la conexión y liberar los recursos
$stmt->close();
$conn->close();
?>