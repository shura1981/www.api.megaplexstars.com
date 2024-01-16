<?php

namespace ApiMegaplex\Io;

use ApiMegaplex\Exceptions\IoException;
use ApiMegaplex\Models\User;
use Exception;

class IoUserJson
{

    static function getUserList($file): array
    {
        // Verificar si el fichero existe
        if (file_exists($file)) {
            // Leer el fichero existente
            $json = file_get_contents($file);
            return json_decode($json, true);
        } else {
            // Crear un array vacío si el fichero no existe
            return array();
        }

    }

    static function saveJson($file, User $user): bool
    {
        try {
            $idExiste = false;

            // obtener lista de usuarios
            $json_data = self::getUserList($file);

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
                'active' => $user->active,
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
            $users = [];
            // obtener lista de usuarios
            $json_data = self::getUserList($file);
            // crear array de User a partir de la lista de usuarios
            foreach ($json_data as $key => $value) {
                $user = new User($value['correo'], $value['contrasena'], $value['rol'], $value['active']);
                $users[] = $user;
            }
            return $users;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }

    static function updateJson($file, User $user): bool
    {

        if ($user == null || $user->correo == null || $user->correo == "") {
            throw new IoException("El correo no puede ser null o vacío", 400);
        }

        try {
            $actualizado = false;
            // obtener lista de usuarios
            $json_data = self::getUserList($file);
            // Buscar por ID y actualizar si existe
            foreach ($json_data as $key => $value) {
                if ($value['correo'] == $user->correo) {
                   $json_data[$key]['rol'] = $user->rol;
                   $json_data[$key]['active'] = $user->active;
                   $json_data[$key]['contrasena'] = $user->contrasena;
                    $actualizado = true;
                    break;
                }
            }
            // actualizar el array en el fichero
            file_put_contents($file, json_encode($json_data, JSON_NUMERIC_CHECK));
            return $actualizado;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }


    static function updatelistJson($file, array $users): bool
    {
        try {
            // actualizar el array en el fichero
            file_put_contents($file, json_encode($users, JSON_NUMERIC_CHECK));
            return true;
        } catch (Exception $e) {
            throw new IoException($e->getMessage(), 500);
        }
    }


}