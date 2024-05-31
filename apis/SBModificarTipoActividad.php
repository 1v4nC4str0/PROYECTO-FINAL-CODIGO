<?php
$config = require('config.php');
$expectedToken = $config['token'];

// Obtener el token de la cabecera de autorización

$headers = apache_request_headers();
$token = null;
if(isset($headers['token'])){
    $token = $headers['token'] ;
} else if(isset($headers['Token'])){
    $token = $headers['Token'] ;
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

// Crear una nueva conexión a la base de datos
$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");

// Establecer el conjunto de caracteres UTF-8
$conn->set_charset("utf8");

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se han proporcionado todos los parámetros necesarios
if (isset($_POST["id"]) && isset($_POST["tipo_actividad_nombre"])) {
    // Obtener los parámetros de manera segura
    $id = $_POST["id"];
    $tipo_actividad_nombre = $_POST["tipo_actividad_nombre"];

    // Consulta SQL con una consulta preparada para actualizar el registro en la tabla masesic_Tipo_Actividad
    $sql = "UPDATE masesic_Tipo_Actividad SET Tipo_Actividad_nombre = ? WHERE Tipo_Actividad_ID = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Verificar si hubo un error al preparar la consulta
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular los parámetros y ejecutar la consulta
    $stmt->bind_param("si", $tipo_actividad_nombre, $id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Verificar si se afectó alguna fila
        if ($stmt->affected_rows > 0) {
            echo "El registro con ID $id ha sido modificado correctamente.";
        } else {
            echo "No se encontró ningún registro que coincida con los criterios especificados.";
        }
    } else {
        echo "Error al ejecutar la consulta: " . $stmt->error;
    }

    // Cerrar la consulta preparada
    $stmt->close();
} else {
    echo "No se han proporcionado todos los parámetros necesarios.";
}

// Cerrar la conexión
$conn->close();
?>
