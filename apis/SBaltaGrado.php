<?php

$config = require('config.php');
$expectedToken = $config['token'];

$grado_Nombre = urldecode($_POST["grado_Nombre"]);
$tipo_grado_Id = urldecode($_POST["tipo_grado_Id"]);


$token = null;
$headers = apache_request_headers();
if (isset($headers['Token'])) {
    $token = $headers['Token'];
} else if (isset($headers['Token'])) {
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
$conn->set_charset('utf8');

// Verificar si hubo un error en la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Preparar la consulta SQL con un marcador de posición (?)
$sql = "INSERT INTO masesic_Grado (grado_Nombre, tipo_grado_Id) VALUES (?, ?)";

// Preparar la sentencia
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la sentencia tuvo éxito
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Enlazar los parámetros con los marcadores de posición
// "ss" indica que ambos parámetros son cadenas (string)
$stmt->bind_param("ss", $grado_Nombre, $tipo_grado_Id);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "El registro con tipo_grado_Id $tipo_grado_Id y grado_Nombre $grado_Nombre ha sido insertado correctamente.";
} else {
    echo "Error al insertar el registro: " . $stmt->error;
}

// Cerrar la sentencia y la conexión
$stmt->close();
$conn->close();
?>
