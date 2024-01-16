<?php
require_once 'middelwares/authorization.php';
use ApiMegaplex\Controllers\ProductosController;
use ApiMegaplex\Models\UserEliteNut;

$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->post('/api/login', 'tokenHeader', ProductosController::class . ':login');
$app->post('/api/cambiar-estado', 'tokenHeader', ProductosController::class . ':cambiarEstado');
$app->post('/api/registre', 'tokenHeader', ProductosController::class . ':registre');
$app->post('/api/remove-count', 'tokenHeader', ProductosController::class . ':removeCount');
$app->get('/api/prueba', 'tokenJwt', ProductosController::class . ':prueba');
$app->get('/api/productos', 'tokenJwt', ProductosController::class . ':getProductos');
$app->post('/api/productos/subir-imagen', 'tokenJwt', ProductosController::class . ':subirImagen');
$app->post('/api/productos/subir-imagen/file', 'tokenJwt', ProductosController::class . ':subirImagenFile');
$app->post('/api/productos', 'tokenJwt', ProductosController::class . ':insertarProducto');

