<?php

use ApiMegaplex\Exceptions\JwtException;
use ApiMegaplex\Jwt\EncodeDecode;

use stdClass;





function tokenHeader()
{
    // Obtener la instancia de Slim
    $app = \Slim\Slim::getInstance();
    if (!validarCabecera($app)) {
        $app->stop();
    }
}


function tokenJwt()
{
    $app = \Slim\Slim::getInstance();
    // obtener jwt de la cabecera de autorización
    $header = (array_key_exists('Authorization', getallheaders())) ? getallheaders()['Authorization'] : null;
    if ($header == null) {
        $result = array('error' => "No se ha enviado el token");
        echo json_encode($result);
        $app->stop();
    }

    // $jwt = trim(str_replace('Bearer', '', $header));
    $jwt = $header;
    try {
        $decoded = EncodeDecode::decode($jwt);
        // Si el token es válido y no ha expirado, podrás acceder a los datos del payload
        // añadir resultado al request
        $app->container->set('jwt', new stdClass());
        $app->container->jwt->decodedToken = $decoded;
        // ahora sigue a la próxima función de middleware o ruta
    } catch (JwtException $e) {
        echo handle_error($app, $e, "Error al validar el token", $e->gethttpCode());
        $app->stop();
    }
}

