<?php
// require_once 'controllers/view-html.php';
use ApiMegaplex\Controllers\ViewHtml;

$app->get('/', function () use ($app) {
    echo URL;
});
$app->get('/home', ViewHtml::class . ':home');