<?php
namespace ApiMegaplex\Controllers;
class ViewHtml
{

    static function home()
    {

        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');
        $app->render('home.php', array('name' => 'World'));
    }

}