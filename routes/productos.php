<?php
require_once 'middelwares/authorization.php';
use ApiMegaplex\Controllers\Productos;
use ApiMegaplex\Models\User;
 
$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->get('/prueba','tokenJwt',   Productos::class . ':prueba');
$app->get('/prueba-decode-jwt','tokenJwt', Productos::class . ':decodeJwt');
$app->post('/prueba-generate-jwt', 'tokenHeader',Productos::class . ':generateJwt');
$app->get('/productos', Productos::class . ':getProductos');
$app->post('/productos/subir-imagen', Productos::class . ':subirImagen');
$app->post('/productos/subir-imagen/file', Productos::class . ':subirImagenFile');
$app->post('/productos', Productos::class . ':insertarProducto');


$app->get('/user',User::class . ':getUser');