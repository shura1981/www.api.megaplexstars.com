<?php

namespace ApiMegaplex\Io;

use ApiMegaplex\Exceptions\IoException;
use ApiMegaplex\Models\User;
use Exception;

class IoUserJson
{
    static function saveJson($file, User $user): bool
    {
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
                if ($value['correo'] == $user->correo) {
                    $idExiste = true;
                    break;
                }
            }

            if ($idExiste) {
                throw new IoException("El usuario ya existe", 400);
            }

            // Añadir nuevo elemento si el ID no existe
            $json_data[] = array(
                'correo' => $user->correo,
                'rol' => $user->rol,
                'contrasena' => $user->contrasena
            );

            // Guardar el array en el fichero
            file_put_contents($file, json_encode($json_data, JSON_NUMERIC_CHECK));
            return true;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }




    }


    /**
     * Obtiene una lista de usuarios.
     * 
     * @return User[] Array de objetos User
     */
    static function readJson($file): array
    {
        try {
            // Verificar si el fichero existe
            $users = [];
            if (file_exists($file)) {
                // Leer el fichero existente
                $json = file_get_contents($file);
                $json_data = json_decode($json, true);
                // crear array de User a partir de los datos del fichero
                foreach ($json_data as $key => $value) {
                    $user = new User($value['correo'], $value['contrasena'], $value['rol']);
                    $users[] = $user;
                }
            }
            return $users;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }


}