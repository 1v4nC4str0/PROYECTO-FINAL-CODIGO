<?php
   $config = require('config.php');
   $expectedToken = $config['token'];
   $tipo_grado_ID=$_POST["tipo_grado_ID"];
   $tipo_grado_nombre = urldecode($_POST["tipo_grado_nombre"]);

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
  
   //Conexion a la base de datos
   //$conn = new mysqli('127.0.0.1','Bruno','contrasena','tfg_android');
   $conn = new mysqli("masesid256.mysql.db", "masesid256", "masESIC2024", "masesid256");
   $conn->set_charset('utf8');
   // Verificar si hubo un error en la conexión
   if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
// Consulta para modificar el registro con el ID recibido
$sql = "UPDATE masesic_TipoGrado SET tipo_grado_nombre=? WHERE tipo_grado_ID = ?";

$stmt = $conn->prepare($sql);

if($stmt == false){
    die("Error al preparar la consulta : "  . $conn->error);
}


$stmt->bind_param("ss", $tipo_grado_nombre,$tipo_grado_ID);


if ($stmt->execute()) {
    echo "El registro con ID $tipo_grado_ID
     ha sido modificado correctamente.";
} else {
    echo "Error al modificar el registro: " . $stmt->error;
}
// Cerrar la conexión

$stmt->close();
$conn->close();
?>
