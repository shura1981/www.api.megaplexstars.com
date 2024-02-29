<?php
use ApiMegaplex\Controllers\ViewHtmlController;
use ApiMegaplex\Controllers\EmailController;

require_once 'middelwares/no-found.php';

$app->get('/', ViewHtmlController::class . ':home');
$app->get('/login', ViewHtmlController::class . ':login');
$app->get('/registre', ViewHtmlController::class . ':registre');
$app->get('/remove-session', ViewHtmlController::class . ':removeCount');
$app->get('/logout', ViewHtmlController::class . ':logOut');
$app->get('/correo', EmailController::class . ':test');
$app->notFound('noFound'); // Manejar rutas no encontradas