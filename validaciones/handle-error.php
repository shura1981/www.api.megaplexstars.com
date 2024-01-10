<?php

function handle_error($app, $error, $message = "Ha ocurrido un error", $code = 500)
{
    $app->response()->status($code);
    $result = array('message' => $message, "error" => $error->getMessage());
    return json_encode($result);
}
