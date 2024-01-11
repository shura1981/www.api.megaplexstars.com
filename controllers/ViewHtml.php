<?php
namespace ApiMegaplex\Controllers;

class ViewHtml
{

    static function home()
    {
        $app = \Slim\Slim::getInstance();
        $key = $app->request()->params('key');
        $app->response->headers->set('Content-Type', 'text/html');

        if ($key == null) {
            // $app->redirect('https://nutramerican.com/');
            $app->redirect(URL_HOST . 'login');
            return;
        }

        $htmlContent= '<div>
        
        <p><b>Gracias por su compra</b>, </p>
        <p>Su pedido ha sido recibido y se encuentra en proceso de envío.</p>
        
        </div>';
        
        $app->render('home.php', array('name' => 'Steven Realpe', 'htmlContent' => $htmlContent));
    }



    static function login()
    {

        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');
        $app->render('login.php', array('url' => URL_HOST));
    }


    static function registre()
    {

        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');
        $app->render('registre.php', array('name' => 'World'));
    }



}


