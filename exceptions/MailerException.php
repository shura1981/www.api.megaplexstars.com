<?php
namespace ApiMegaplex\Exceptions;

use Exception;

class MailerException extends Exception
{
    // Puedes agregar más propiedades si es necesario
    protected $httpCode;

    public function __construct($message, $httpCode = 0, $code = 0, Exception $previous = null)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code, $previous);
    }

    public function gethttpCode()
    {
        return $this->httpCode;
    }
}
