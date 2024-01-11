<?php

namespace ApiMegaplex\Controllers;


use ApiMegaplex\Jwt\EncodeDecode;
use ApiMegaplex\Io\IoProudctJson;
use ApiMegaplex\Io\IoProductoImage;
use ApiMegaplex\Models\User;
use ApiMegaplex\Exceptions\IoException;
use Exception;


class ProductosController
{
    static function prueba()
    {
        $app = \Slim\Slim::getInstance();
        $nombre = $app->request()->params('nombre');
        $dbHost = $_ENV['DB_HOST'];
        $path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public');
        $decodedToken = $app->container->jwt->decodedToken;

        $result = array('saludo' => "Hola $nombre", "dir" => __DIR__, "dbHost" => $dbHost, "path" => $path, "jwt" => $decodedToken);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function login()
    {
        $app = \Slim\Slim::getInstance();
        try {
            $data = json_decode($app->request()->getBody());

            if (!validarCampo($data, 'correo', 'el campo correo es obligatorio')) {
                return; // Detiene la ejecución si hay un error de validación
            }
            if (!validarCampo($data, 'password', 'el campo password es obligatorio')) {
                return; // Detiene la ejecución si hay un error de validación
            }
            $decodedToken = $app->container->jwt->decodedToken;
            $userDecode = $decodedToken->user;
            $correo = $userDecode->correo;
            $user = User::obtenerUsuario($correo);

            if ($user == null) {
                $app->response()->status(401);
                $response = array('message' => 'Usuario no existe');
                echo json_encode($response);
                return;
            }

            if ($user->contrasena != $data->password) {
                $app->response()->status(401);
                $response = array('message' => 'Contraseña incorrecta');
                echo json_encode($response);
                return;
            }

            $app->response()->status(200);
            $response = array('message' => 'Bienvenido ' . $correo);
            echo json_encode($response);
        } catch (Exception $e) {
            // Manejar otras excepciones (token inválido, error en la decodificación, etc.)
            echo handle_error($app, $e);
        }
    }
    static function registre()
    {
        $app = \Slim\Slim::getInstance();
        try {
            $data = json_decode($app->request()->getBody());
            //validar campos obligatorios
            if (!self::validarCamposRegistre($data)) {
                return;
            }
            $jwt = EncodeDecode::encode($data);
            User::createUser($data->correo, $data->password, $data->rol);
            $result = array('token' => $jwt, 'message' => 'Usuario creado');
            $app->response()->status(201);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            echo handle_error($app, $e);
        }
    }
    static function getProductos()
    {
        $app = \Slim\Slim::getInstance(); // obtener instancia de Slim para acceder a los métodos
        $file = $_ENV['FILE_JSON']; // Ruta del fichero JSON
        try {
            $json_data = IoProudctJson::readJson($file); // Leer el fichero JSON
            $app->response()->status(200); // Código de respuesta
            $result = array('status' => 'true', 'payload' => $json_data); // Respuesta
        } catch (IoException $e) {
            $app->response()->status($e->gethttpCode());
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function subirImagen()
    {
        $app = \Slim\Slim::getInstance(); // obtener instancia de Slim para acceder a los métodos
        $data = json_decode($app->request()->getBody()); // Obtener el JSON enviado
        //validar campos obligatorios
        if (!validarCampo($data, 'image', 'El campo image es requerido')) {
            return;
        }

        try {
            $imageBase64 = $data->image; // Obtener la imagen en base64
            $url = IoProductoImage::saveImageFromBase64($imageBase64); // Guardar la imagen y obtener la URL
            $app->response()->status(201); // Código de respuesta
            $result = array('status' => 'true', 'message' => 'Imagen subida', 'url' => $url); // Respuesta
        } catch (IoException $e) {
            $app->response()->status($e->gethttpCode());
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }

        echo json_encode($result, JSON_NUMERIC_CHECK);

    }
    static function subirImagenFile()
    {
        $app = \Slim\Slim::getInstance(); // obtener instancia de Slim para acceder a los métodos
        try {
            if (!validarFile("image", "No se ha definido el key file en form-data")) { // Validar el campo file
                return;
            }
            // Acceder al archivo enviado
            $file = $_FILES['image'];
            $url = IoProductoImage::saveImageFromFile($file);
            $app->response()->status(201);
            $result = array('status' => 'true', 'message' => 'Imagen subida', 'url' => $url);
        } catch (Exception $e) {
            $app->response()->status(500);
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }

        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function insertarProducto()
    {
        $app = \Slim\Slim::getInstance(); // obtener instancia de Slim para acceder a los métodos
        $file = $_ENV['FILE_JSON']; // Ruta del fichero JSON
        $data = json_decode($app->request()->getBody()); // Obtener el JSON enviado

        //validar campos obligatorios
        if (!self::validarCamposInsertarProductos($data)) {
            return;
        }

        try {
            $isNew = IoProudctJson::saveJson($file, $data); //Guardar el json en un fichero, devuelve true si se actualizó, false si se añadió
            $mensaje = $isNew ? 'Producto actualizado' : 'Producto añadido'; // Mensaje de respuesta
            $isNew ? $app->response()->status(200) : $app->response()->status(201); // Código de respuesta
            $result = array('status' => 'true', 'message' => $mensaje); // Respuesta
        } catch (IoException $e) {
            $app->response()->status($e->gethttpCode());
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }

        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function validarCamposInsertarProductos($data)
    {
        if (!validarCampo($data, 'id', 'el campo id es obligatorio')) {
            return false; // Detiene la ejecución si hay un error de validación
        }
        if (!validar($data, 'available', 'el campo available es obligatorio')) {
            return false; // Detiene la ejecución si hay un error de validación
        }
        if (!validarCampo($data, 'description', 'el campo description es obligatorio')) {
            return false; // Detiene la ejecución si hay un error de validación
        }
        if (!validarCampo($data, 'name', 'el campo name es obligatorio')) {
            return false; // Detiene la ejecución si hay un error de validación
        }
        if (!validarCampo($data, 'price', 'el campo price es obligatorio')) {
            return false; // Detiene la ejecución si hay un error de validación
        }

        return true;
    }
    static function validarCamposRegistre($data)
    {
        if (
            !validarCampo($data, 'correo', 'El campo correo es requerido') ||
            !validarCampo($data, 'rol', 'El campo rol es requerido') || !validarCampo($data, 'password', 'El campo password es requerido')
        ) {
            return false; // Detiene la ejecución si hay un error de validación
        }
        return true;
    }
}