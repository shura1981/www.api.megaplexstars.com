<?php
require_once 'validaciones/cors.php';

set_time_limit(0);
ini_set('allow_url_fopen', 1);
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '500M');
ini_set('max_input_time', 4000); // Play with the values
ini_set('max_execution_time', 4000); // Play with the values
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
date_default_timezone_set('America/Bogota');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}


