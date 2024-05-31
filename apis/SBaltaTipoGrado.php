<?php

$config = require('config.php');
$expectedToken = $config['token'];

$tipo_grado_nombre = urldecode($_POST["tipo_grado_nombre"]);


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


//$conn = new mysqli('127.0.0.1','Bruno','contrasena','tfg_android');
$conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
   $conn->set_charset('utf8');

    // Verificar si hubo un error en la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    // Consulta para eliminar el registro con el ID recibido
    $sql = "INSERT INTO masesic_TipoGrado (tipo_grado_nombre) VALUES (?)";


    $stmt = $conn ->prepare($sql);


    if($stmt == false){
        die("Error al preparar la consulta: "  . $conn->error);
    }


    $stmt->bind_param("s",$tipo_grado_nombre);


    if ($stmt->execute()) {
        echo "El registro con nomnbre $tipo_grado_nombre ha sido insertado correctamente.";
    } else {
        echo "Error al insertar el registro: " . $stmt->error;
    }
    // Cerrar la conexión


    $stmt->close();
    $conn->close();
?>