<?php
require_once 'middelwares/authorization.php';
use ApiMegaplex\Controllers\Productos;
use ApiMegaplex\Models\User;

$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->get('/api/prueba', 'tokenJwt', Productos::class . ':prueba');
$app->get('/api/login', 'tokenJwt', Productos::class . ':login');
$app->post('/api/registre', 'tokenHeader', Productos::class . ':registre');
$app->get('/api/productos', Productos::class . ':getProductos');
$app->post('/api/productos/subir-imagen', Productos::class . ':subirImagen');
$app->post('/api/productos/subir-imagen/file', Productos::class . ':subirImagenFile');
$app->post('/api/productos', Productos::class . ':insertarProducto');


$app->get('/user', User::class . ':getUser');//Consume base de datos