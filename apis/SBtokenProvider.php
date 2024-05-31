<?php
$config = require('config.php');  
header('Content-Type: application/json');  
echo json_encode([
    'token' => $config['token']
]);
