<?php
namespace ApiMegaplex\Controllers;

use ApiMegaplex\Models\User;
use Exception;

class UserController
{


    static function obtenerUsuarios()
    {
        try {
            $app = \Slim\Slim::getInstance();
            $users = User::obtenerUsuarios();
            $app->response()->status(200);
            echo json_encode($users);

        } catch (Exception $e) {
            echo handle_error($app, $e);
        }
    }




}