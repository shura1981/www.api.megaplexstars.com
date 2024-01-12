<?php

namespace ApiMegaplex\Controllers;

use ApiMegaplex\Mailer\SenderEmail;
use Exception;
class EmailController
{

    static function test()
    {
        $app = \Slim\Slim::getInstance();
        try {
            $sender = new SenderEmail();
            $message = $sender->sendTest();
            $result = array('status' => 'true', 'message' => $message);
            $app->response()->status(200);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


}