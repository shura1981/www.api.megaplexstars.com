<?php
require_once 'middelwares/authorization.php';
use ApiMegaplex\Controllers\UserController;

$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->get('/api/users/list', UserController::class . ':obtenerUsuarios');//Consume base de datos