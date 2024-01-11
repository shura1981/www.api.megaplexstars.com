<?php
namespace ApiMegaplex\Io;

use Exception;
use ApiMegaplex\Exceptions\IoException;

class IoProductoImage
{

    static function saveImageFromFile($file)
    {
        if ($file == null) {
            throw new IoException("El campo file es null", 400);
        }


        try {
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
                throw new IoException('Error al guardar la imagen', 500);
            }

            $host = explode('.com/', URL_HOST)[0];
            return $host . ".com/" . $imagePath;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }

    static function saveImageFromBase64($imageBase64)
    {

        if ($imageBase64 == null || $imageBase64 == '') {
            throw new IoException("No se ha definido el campo image con base64", 400);
        }

        try {
            $imageName = time() . '.png';
            $imagePath = $_ENV['PATH_UPLOAD_IMAGES'] . $imageName;
            file_put_contents($imagePath, base64_decode($imageBase64));
            $host = explode('.com/', URL_HOST)[0];
            return $host . ".com/" . $imagePath;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }

}