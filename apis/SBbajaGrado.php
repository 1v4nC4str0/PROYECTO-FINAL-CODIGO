<?php

    $grado_id=$_POST["grado_id"];
    //Conexion a la base de datos
    //$conn = new mysqli('127.0.0.1','Bruno','contrasena','tfg_android');

    $config = require('config.php');
$expectedToken = $config['token'];


$token = null;
$headers = apache_request_headers();
if (isset($headers['token'])) {
    $token = $headers['token'];
}else if (isset($headers['Token'])) {
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
    // Verificar si hubo un error en la conexión
    if ($conn->connect_error) {
      die("Error de conexión: " . $conn->connect_error);
    }
    // Consulta para eliminar el registro con el ID recibido
    $sql = "DELETE FROM masesic_Grado WHERE grado_id = ?";

    $stmt = $conn->prepare($sql);


    if($stmt == false){
      die("Error al preparar a consulta: " . $conn->error);
    }


    $stmt->bind_param("i", $grado_id);


    if ($stmt->execute()) {
       echo "El registro con ID $grado_id ha sido eliminado correctamente.";
    } else {
     echo "Error al eliminar el registro: " . $stmt->error;
    }
    // Cerrar la conexión
    $stmt->close();
    $conn->close();
?>
