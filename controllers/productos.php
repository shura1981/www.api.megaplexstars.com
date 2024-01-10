<?php

namespace ApiMegaplex\Controllers;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;


class Productos
{
    static function prueba()
    {
        global $app;
        $nombre = $app->request()->params('nombre');
        $dbHost = $_ENV['DB_HOST'];
        $path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public');
        $decodedToken = $app->container->jwt->decodedToken;

        $result = array('saludo' => "Hola $nombre", "dir" => __DIR__, "dbHost" => $dbHost, "path" => $path, "jwt" => $decodedToken);
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function decodeJwt()
    {
        $app = \Slim\Slim::getInstance();


        try {
            $decodedToken = $app->container->jwt->decodedToken;

            // Si el token es válido y no ha expirado, podrás acceder a los datos del payload
            $app->response()->status(200);
            echo json_encode($decodedToken, JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            // Manejar otras excepciones (token inválido, error en la decodificación, etc.)

            echo handle_error($app, $e);
        }
    }
    static function generateJwt()
    {
        $app = \Slim\Slim::getInstance();

        $data = json_decode($app->request()->getBody());

        if (
            !validarCampo($data, 'correo', 'El campo correo es requerido') ||
            !validarCampo($data, 'rol', 'El campo rol es requerido') || !validarCampo($data, 'password', 'El campo password es requerido')
        ) {
            return;
        }


        try {
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
            $result = array('token' => $jwt);
            $app->response()->status(201);
            echo json_encode($result, JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            echo handle_error($app, $e);
        }
    }
    static function getProductos()
    {
        global $app;
        $file = $_ENV['FILE_JSON'];
        if (!validarCabecera($app)) {
            return;
        }

        try {
            // Verificar si el fichero existe
            if (file_exists($file)) {
                // Leer el fichero existente
                $json = file_get_contents($file);
                $json_data = json_decode($json, true);
            } else {
                // Crear un array vacío si el fichero no existe
                $json_data = array();
            }

            $app->response()->status(200);
            $result = array('status' => 'true', 'payload' => $json_data);
        } catch (Exception $e) {
            $app->response()->status(500);
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }


        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function subirImagen()
    {
        global $app;
        if (!validarCabecera($app)) {
            return;
        }

        try {
            $data = json_decode($app->request()->getBody());
            $image = $data->image;
            $imageName = time() . '.png';
            $imagePath = $_ENV['PATH_UPLOAD_IMAGES'] . $imageName;
            file_put_contents($imagePath, base64_decode($image));
            $app->response()->status(200);
            $path = $_ENV['PATH_URL_IMAGES'] . $imageName;
            $url = $_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/' . $path : 'https://' . $path;
            $app->response()->status(201);
            $result = array('status' => 'true', 'message' => 'Imagen subida', 'url' => $url);
        } catch (Exception $e) {
            $app->response()->status(500);
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }

        echo json_encode($result, JSON_NUMERIC_CHECK);

    }
    static function subirImagenFile()
    {
        global $app;

        if (!validarCabecera($app)) {
            return;
        }

        try {
            // Verificar si el archivo ha sido enviado
            if (!isset($_FILES['image'])) {
                throw new Exception('No se ha enviado ninguna imagen.');
            }

            // Acceder al archivo enviado
            $file = $_FILES['image'];

            // Puedes agregar aquí validaciones adicionales (por ejemplo, tipo de archivo, tamaño)
            // conocer la extensión del archivo
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            // Validar la extensión
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                throw new Exception('El archivo debe ser una imagen JPG, JPEG, WEBP o PNG.');
            }

            // Generar un nombre único para el archivo
            $imageName = time() . '.' . $extension;
            $imagePath = $_ENV['PATH_UPLOAD_IMAGES'] . $imageName;

            // Mover el archivo subido a la carpeta de destino
            if (!move_uploaded_file($file['tmp_name'], $imagePath)) {
                throw new Exception('Error al guardar la imagen.');
            }

            $app->response()->status(201);
            $path = $_ENV['PATH_URL_IMAGES'] . $imageName;
            $url = $_SERVER['HTTP_HOST'] == 'localhost' ? 'http://localhost/' . $path : 'https://' . $path;

            $result = array('status' => 'true', 'message' => 'Imagen subida', 'url' => $url);
        } catch (Exception $e) {
            $app->response()->status(500);
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }

        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
    static function insertarProducto()
    {
        global $app;
        $file = $_ENV['FILE_JSON'];
        if (!validarCabecera($app)) {
            return;
        }
        $data = json_decode($app->request()->getBody());
        //validar campos obligatorios
        if (!validarCampo($data, 'id', 'el campo id es obligatorio')) {
            return; // Detiene la ejecución si hay un error de validación
        }
        if (!validar($data, 'available', 'el campo available es obligatorio')) {
            return; // Detiene la ejecución si hay un error de validación
        }
        if (!validarCampo($data, 'description', 'el campo description es obligatorio')) {
            return; // Detiene la ejecución si hay un error de validación
        }
        if (!validarCampo($data, 'name', 'el campo name es obligatorio')) {
            return; // Detiene la ejecución si hay un error de validación
        }
        if (!validarCampo($data, 'price', 'el campo price es obligatorio')) {
            return; // Detiene la ejecución si hay un error de validación
        }

        try {
            $idExiste = false;

            // Verificar si el fichero existe
            if (file_exists($file)) {
                // Leer el fichero existente
                $json = file_get_contents($file);
                $json_data = json_decode($json, true);
            } else {
                // Crear un array vacío si el fichero no existe
                $json_data = array();
            }

            // Buscar por ID y actualizar si existe
            foreach ($json_data as $key => $value) {
                if ($value['id'] == $data->id) {
                    $json_data[$key]['available'] = $data->available;
                    $json_data[$key]['description'] = $data->description;
                    $json_data[$key]['picture'] = $data->picture;
                    $json_data[$key]['price'] = $data->price;
                    $json_data[$key]['name'] = $data->name;
                    $idExiste = true;
                    break;
                }
            }

            // Añadir nuevo elemento si el ID no existe
            if (!$idExiste) {
                $json_data[] = array(
                    'id' => $data->id,
                    'available' => $data->available,
                    'description' => $data->description,
                    'picture' => $data->picture,
                    'price' => $data->price,
                    'name' => $data->name
                );
            }

            // Guardar el array en el fichero
            file_put_contents($file, json_encode($json_data, JSON_NUMERIC_CHECK));
            $app->response()->status(200);
            $result = array('status' => 'true', 'message' => 'Producto actualizado o añadido');
        } catch (Exception $e) {
            $app->response()->status(500);
            $result = array('status' => 'false', 'message' => 'Ocurrió un error: ' . $e->getMessage());
        }


        echo json_encode($result, JSON_NUMERIC_CHECK);
    }
}