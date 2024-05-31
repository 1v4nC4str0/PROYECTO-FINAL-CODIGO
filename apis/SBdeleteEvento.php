<?php
    $config = require('config.php');
    $expectedToken = $config['token'];    
    $evento_id=$_POST["id"];
    $servername = "masesid256.mysql.db";
    $username = "masesid256";
    $password = "masESIC2024";
    $dbname = "masesid256";

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

    if ($conn->connect_error) {
        die("ERROR".$conn->connect_error);
    }

    $sql = "DELETE FROM masesic_Evento WHERE Evento_id = '$evento_id'";

    if($conn->query($sql)===TRUE){
        echo "EL REGISTRO CON ID $evento_id SE HA BORRADO";
    } else {
        echo "ERROR DELETE".$conn->error;
    }
    $conn->close();
    
?>