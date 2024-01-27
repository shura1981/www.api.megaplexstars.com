<?php
use ApiMegaplex\Controllers\cronJobWhatsapp;

require_once 'middelwares/authorization.php';
$app->response()->header('Content-Type', 'application/json;charset=UTF-8'); //Para que devuelva un json por defecto
$app->post('/api/cronJobWhatsapp/create', 'tokenHeader', cronJobWhatsapp::class . ':createCronJob');