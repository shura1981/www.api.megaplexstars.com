<?php

$app->get('/video', function () use ($app) {
    if (isset($_GET['key']) && $_GET['key'] == '123') {
        // do something
        $filename = "public/videos/estudiosxl.mp4";
        $app->response()->header('Content-Type', mime_content_type($filename));
        readfile($filename);
        exit;
    } else {
        // status 401 Unauthorized
        $app->response()->status(401);
        echo 'no tienes acceso a este archivo ';
    }
});
$app->get('/image', function () use ($app) {
    if (isset($_GET['key']) && $_GET['key'] == '123') {
        // do something
        $filename = "public/images/loadstudiosxl.webp";
        $app->response()->header('Content-Type', mime_content_type($filename));
        readfile($filename);
        exit;
    } else {
        // status 401 Unauthorized
        $app->response()->status(401);
        echo 'no tienes acceso a este archivo ';
    }
});
$app->get('/json', function () use ($app) {
    if (isset($_GET['key']) && $_GET['key'] == '123') {
        // do something
        $filename = "public/json/productos.json";
        $app->response()->header('Content-Type', 'application/json;charset=UTF-8');
        readfile($filename);
        exit;
    } else {
        // status 401 Unauthorized
        $app->response()->status(401);
        echo 'no tienes acceso a este archivo ';
    }

});