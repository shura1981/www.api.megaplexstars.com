<?php

/**
 * valida el token de la cabecera
 */
function validarCabecera($app)
{
    // $header= getallheaders();
    $key = $_ENV['KEY_HEADER'];
    $header = (array_key_exists('Authorization', getallheaders())) ? getallheaders()['Authorization'] : null;
    if ($header != $key) {
        $app->response()->status(401);
        $result = array( 'error' => 'No autorizado', 'message' => "No se ha enviado el token");
        echo json_encode($result, JSON_NUMERIC_CHECK);
        return false;
    }
    return true;
}

function ErrorServidor($app, $e)
{
    $app->response()->header('X-Status-Reason', $e->getMessage());
    $app->response()->status(500);
    return array('status' => 'false', 'message' => 'Ocurrió un error.' . $e->getMessage());
}


function validarCampo($data, $campo, $mensajeError = '')
{
    if (!isset($data->$campo) || empty($data->$campo)) {
        global $app;
        $app->response()->status(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => $mensajeError ?: "El campo '$campo' no está presente o está vacío"
        ]);
        return false;
    }
    return true;
}

function validar($data, $campo, $mensajeError = '')
{
    if (!isset($data->$campo)) {
        global $app;
        $app->response()->status(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => $mensajeError ?: "El campo '$campo' no está presente o está vacío"
        ]);
        return false;
    }
    return true;
}


function validarFile($campo, $mensajeError = '')
{
      // Verificar si el archivo ha sido enviado
    if (!isset($_FILES[$campo])) {
        global $app;
        $app->response()->status(400); // Bad Request
        echo json_encode([
            'status' => 'error',
            'message' => $mensajeError ?: "El campo '$campo' no está presente o está vacío"
        ]);
        return false;
    }
    return true;
}
