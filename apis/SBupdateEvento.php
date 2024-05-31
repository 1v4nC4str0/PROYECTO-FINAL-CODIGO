<?php
$config = require('config.php');
$expectedToken = $config['token'];
$servername = "masesid256.mysql.db";
$username = "masesid256";
$password = "masESIC2024";
$dbname = "masesid256";

$evento_id=$_POST["id"];
$evento_nombre = $_POST["name"];
$evento_fecha = $_POST["fecha"];
$evento_descripcion = $_POST["descripcion"];
$evento_foto = $_POST["foto"];
$evento_lugar = $_POST["lugar"];
$evento_hora = $_POST["hora"];
$evento_formato = $_POST["formato"];

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


$sql = "UPDATE masesic_Evento SET Evento_nombre=?, Evento_fecha=?, Evento_descripcion=?,
    Evento_foto=?, Evento_lugar=?, Evento_hora=?,
    Evento_formato=? WHERE Evento_id=?";

$stmt = $conn->prepare($sql);


if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}


$stmt->bind_param("sssssssi", $evento_nombre, $evento_fecha, $evento_descripcion, $evento_foto, $evento_lugar, $evento_hora, $evento_formato, $evento_id);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "El registro con ID $grado_id ha sido modificado correctamente.";
} else {
    echo "Error al modificar el registro: " . $stmt->error;
}

// Cerrar la sentencia y la conexión
$stmt->close();

$conn->close();
