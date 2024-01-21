<?php
namespace ApiMegaplex\Controllers;

use Exception;

class ApiAddiController
{

    static function obtenerToken()
    {
        try {
            $app = \Slim\Slim::getInstance();
            $ADDI_AUDIENCE = $_ENV['ADDI_AUDIENCE'];
            $GRANT_TYPE = $_ENV['GRANT_TYPE'];
            $CLIENT_ID = $_ENV['CLIENT_ID'];
            $CLIENT_SECRET = $_ENV['CLIENT_SECRET'];

            $data = array(
                "audience" => $ADDI_AUDIENCE,
                "grant_type" => $GRANT_TYPE,
                "client_id" => $CLIENT_ID,
                "client_secret" => $CLIENT_SECRET
            );
            
            $ch = curl_init('https://auth.addi-staging.com/oauth/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // obtener el código de la respuesta
            curl_close($ch);
            $response = json_decode($response, true);
            $app->response()->status($httpcode);
            echo json_encode($response);
        } catch (Exception $e) {
            echo handle_error($app, $e);
        }
    }


}
