<?php
require_once 'vendor/autoload.php';//CARGAMOS LOS PAQUETES DE COMPOSER
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);// CARGAMOS LAS VARIABLES DE ENTORNO ANTES DE CARGAR LOS DEMÁS ARCHIVOS PARA QUE PUEDAN USAR LAS VARIABLES DE ENTORNO
$dotenv->load();

// require_once '../connection_mysql/connection_intranet.php';
require_once 'partials/headers.php';
require_once 'validaciones/handle-error.php';
require_once 'validaciones/validator.php';
require_once 'controllers/productos.php';

use ApiMegaplex\Controllers\Productos;

$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto

$app->get('/prueba', Productos::class . ':prueba');
$app->get('/prueba-decode-jwt', Productos::class . ':decodeJwt');
$app->post('/prueba-generate-jwt', Productos::class . ':generateJwt');
$app->get('/productos', Productos::class . ':getProductos');
$app->post('/productos/subir-imagen', Productos::class . ':subirImagen');
$app->post('/productos/subir-imagen/file', Productos::class . ':subirImagenFile');
$app->post('/productos', Productos::class . ':insertarProducto');
$app->run();//Inicia el Api



