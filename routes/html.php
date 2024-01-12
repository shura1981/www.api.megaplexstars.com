<?php
use ApiMegaplex\Controllers\ViewHtmlController;
use ApiMegaplex\Controllers\EmailController;

$app->get('/', ViewHtmlController::class . ':home');
$app->get('/login', ViewHtmlController::class . ':login');
$app->get('/registre', ViewHtmlController::class . ':registre');
$app->get('/correo', EmailController::class . ':test');