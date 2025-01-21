<?php
require_once 'middelwares/authorization.php';
use ApiMegaplex\Controllers\ApiSisteCreditoController;

$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->post('/siste-credito/crear-orden', 'tokenHeader', ApiSisteCreditoController::class . ':create');
$app->get('/siste-credito/consultar-orden', 'tokenHeader', ApiSisteCreditoController::class . ':query');
 