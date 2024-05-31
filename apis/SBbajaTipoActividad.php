<?php
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



// Crear una nueva conexión a la base de datos
$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");

// Establecer el conjunto de caracteres UTF-8
$conn->set_charset("utf8");

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se ha proporcionado el parámetro ID
if (isset($_POST["id"])) {
    // Obtener el parámetro ID de manera segura
    $id = $_POST["id"];

    // Consulta SQL con una consulta preparada para eliminar el registro de la tabla masesic_Tipo_Actividad
    $sql = "DELETE FROM masesic_Tipo_Actividad WHERE Tipo_Actividad_ID = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Verificar si hubo un error al preparar la consulta
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular el parámetro y ejecutar la consulta
    $stmt->bind_param("s", $id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Verificar si se afectaron filas
        if ($stmt->affected_rows > 0) {
            echo "El registro ha sido eliminado correctamente.";
        } else {
            echo "No se encontró ningún registro que coincida con los criterios especificados.";
        }
    } else {
        echo "Error al ejecutar la consulta: " . $stmt->error;
    }

    // Cerrar la consulta preparada
    $stmt->close();
}

// Cerrar la conexión
$conn->close();
?>
