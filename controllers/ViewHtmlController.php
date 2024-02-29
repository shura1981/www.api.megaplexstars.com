<?php
namespace ApiMegaplex\Controllers;

use ApiMegaplex\Models\UserTest;
use Exception;

class ViewHtmlController
{


    static private function getIdSession(): int
    {
        session_start();
        if (!isset($_SESSION['id_user'])) {
            return 0;
        }
        return $_SESSION['id_user'];
    }

    static function home()
    {
        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');

        $idSession = self::getIdSession();

        if ($idSession == 0) {
            $app->render('login.php', array('url' => URL_HOST));
            return;
        }

        $htmlContent = "<div>
        <p><b>Gracias por su compra $idSession </b>, </p>
        <p>Su pedido ha sido recibido y se encuentra en proceso de envío.</p>
        </div>";

        $app->render('home.php', array('name' => 'Steven Realpe', 'htmlContent' => $htmlContent, 'url' => URL_HOST));
    }


    static function logOut()
    {
        session_start();
        session_destroy();
        $app = \Slim\Slim::getInstance();
        $app->redirect(URL_HOST);
    }
    static function login()
    {

        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');

        if (self::getIdSession() != 0) {
            $app->redirect(URL_HOST);
            return;
        }

        $email = $app->request()->params('email');
        $pass = $app->request()->params('pass');

        if ($email != null && $pass != null) {
            try {
                $user = UserTest::getUser($email, $pass);

                if ($user) {
                    $_SESSION['id_user'] = $user->id;
                    $app->redirect(URL_HOST);
                    return;
                } else {
                    $app->render('login.php', array('url' => URL_HOST, 'error' => 'no encontrado'));
                    return;
                }

            } catch (Exception $e) {
                $app->render('login.php', array('url' => URL_HOST, 'error' => 'no encontrado'));
            }
        }




        $app->render('login.php', array('url' => URL_HOST));
    }


    static function registre()
    {

        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');
        $app->render('registre.php', array('name' => 'World'));
    }
    static function removeCount()
    {
        $app = \Slim\Slim::getInstance();
        $app->response->headers->set('Content-Type', 'text/html');
        $app->render('remove-count.php');
    }




}


