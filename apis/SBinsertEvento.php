<?php
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

$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$evento_nombre = $_POST["name"];
$evento_fecha = $_POST["fecha"];
$evento_descripcion = $_POST["descripcion"];
$evento_foto = $_POST["foto"];
$evento_lugar = $_POST["lugar"];
$evento_hora = $_POST["hora"];
$evento_formato = $_POST["formato"];

$sql = "INSERT INTO masesic_Evento (Evento_nombre, Evento_fecha, Evento_descripcion, Evento_foto, Evento_lugar, Evento_hora, Evento_formato)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

$stmt->bind_param("sssssss", $evento_nombre, $evento_fecha, $evento_descripcion, $evento_foto, $evento_lugar, $evento_hora, $evento_formato);

if ($stmt->execute()) {
    echo "El registro ha sido insertado correctamente.";
} else {
    echo "Error al insertar el registro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
