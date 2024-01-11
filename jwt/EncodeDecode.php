<?php
namespace ApiMegaplex\Jwt;


use ApiMegaplex\Exceptions\JwtException;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class EncodeDecode
{
    static function encode($data)
    {
        $key = $_ENV['KEY_SECRET']; // Asegúrate de usar una clave segura y única
        $time = time() + (60 * 60); // 1 hora de expiración

        $correo = $data->correo;
        $rol = $data->rol;

        $payload = array(
            "user" => array("correo" => $correo, "rol" => $rol),  // Emisor
            "iat" => time(),                   // Tiempo de creación
            "exp" => $time,         // Expiración (1 hora en este caso)
            // Puedes agregar más campos según lo necesites
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }
    static function decode($jwt)
    {
        try {
            $key = $_ENV['KEY_SECRET']; // La misma clave que usaste para codificar
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            // Manejar la excepción si el token ha expirado
            throw new JwtException("El token ha expirado", 401);
        } catch (Exception $e) {
            // Manejar otras excepciones (token inválido, error en la decodificación, etc.)
            if ($e->getMessage() == "Signature verification failed") {
                throw new JwtException("El token es inválido", 401);
            }
            throw new JwtException($e->getMessage(), 500);
        }
    }
}


