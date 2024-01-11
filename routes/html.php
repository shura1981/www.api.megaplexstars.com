<?php
use ApiMegaplex\Controllers\ViewHtml;
use ApiMegaplex\Controllers\Email;

$app->get('/', ViewHtml::class . ':home');
$app->get('/login', ViewHtml::class . ':login');
$app->get('/registre', ViewHtml::class . ':registre');
$app->get('/correo', Email::class . ':test');