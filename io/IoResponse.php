<?php

namespace ApiMegaplex\Io;

class IoResponse
{
    static function responseSave($response)
    {
        // guardar la respuesta en la carpeta log
        $log = fopen("log/log.txt", "a");
        fwrite($log, date("Y-m-d H:i:s") . " - " . json_encode($response, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    }
}
