<?php
require_once 'vendor/autoload.php';//CARGAMOS LOS PAQUETES DE COMPOSER
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);// CARGAMOS LAS VARIABLES DE ENTORNO ANTES DE CARGAR LOS DEMÁS ARCHIVOS PARA QUE PUEDAN USAR LAS VARIABLES DE ENTORNO
$dotenv->load();

// require_once '../connection_mysql/connection_intranet.php';
require_once 'partials/headers.php';
require_once 'validaciones/handle-error.php';
require_once 'validaciones/validator.php';

$app = new \Slim\Slim();

define('URL',$_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);


require_once 'routes/productos.php';
require_once 'routes/html.php';

$app->run();//Inicia el Api



