<?php
require_once 'middelwares/authorization.php';
use ApiMegaplex\Controllers\ApiAddiController;

$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->post('/addi/obtener-token', 'tokenHeader', ApiAddiController::class . ':obtenerToken');
$app->post('/addi/obtener-url', 'tokenHeader', ApiAddiController::class . ':obtenerUrlRedireccion');
 