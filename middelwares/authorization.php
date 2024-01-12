<?php

use ApiMegaplex\Exceptions\JwtException;
use ApiMegaplex\Jwt\EncodeDecode;
use ApiMegaplex\Models\User;

// use stdClass;





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
        $app->response()->status(401);
        $result = array('error' => "No se ha enviado el token", 'message' => "No autorizado");
        echo json_encode($result);
        $app->stop();
    }

    // $jwt = trim(str_replace('Bearer', '', $header));
    $jwt = $header;
    try {
        $decoded = EncodeDecode::decode($jwt);
        $correo = $decoded->user->correo;

        $user = User::obtenerUsuario($correo);

        if ($user == null) {
            $app->response()->status(401);
            $response = array('error' => 'Error en la validación de credenciales', 'message' => 'Usuario no registrado');
            echo json_encode($response);
            $app->stop();
        }

        if ($user->active == 0) {
            $app->response()->status(401);
            $response = array('error' => 'Error en la validación de credenciales', 'message' => 'Usuario desactivado');
            echo json_encode($response);
            $app->stop();
        }

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

