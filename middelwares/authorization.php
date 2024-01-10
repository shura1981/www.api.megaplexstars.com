<?php
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;







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
        $key = $_ENV['KEY_SECRET']; // La misma clave que usaste para codificar
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $result = array('data' => $decoded);
        // Si el token es válido y no ha expirado, podrás acceder a los datos del payload
        // añadir resultado al request
        $app->container->set('jwt', new stdClass());
        $app->container->jwt->decodedToken = $decoded;

        // ahora sigue a la próxima función de middleware o ruta

    } catch (\Firebase\JWT\ExpiredException $e) {
        // Manejar la excepción si el token ha expirado
        echo handle_error($app, $e, "El token ha expirado", 401);
        $app->stop();
    } catch (Exception $e) {
        // Manejar otras excepciones (token inválido, error en la decodificación, etc.)
        if ($e->getMessage() == "Signature verification failed") {
            echo handle_error($app, $e, "El token es inválido", 401);
            $app->stop();
        }
        echo handle_error($app, $e);
        $app->stop();
    }
}

