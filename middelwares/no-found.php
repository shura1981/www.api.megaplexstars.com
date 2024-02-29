<?php 

function noFound()
{
    $app = \Slim\Slim::getInstance();
    $app->response->setStatus(404);
    $app->response->headers->set('Content-Type', 'text/html');
    $app->render('no-found.php');
}