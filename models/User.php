<?php
namespace ApiMegaplex\Models;

use ApiMegaplex\Io\IoUserJson;
use ApiMegaplex\Exceptions\IoException;
use Exception;
use JsonSerializable; // Add this import statement


class User implements JsonSerializable
{
    public function jsonSerialize()
    {
        return [
            'correo' => $this->correo,
            'rol' => $this->rol
        ];
    }
    public $correo;
    public $contrasena;
    public $rol;

    public $active;

    public function __construct($correo, $contrasena, $rol, $active = 1)
    {
        $this->correo = $correo;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
        $this->active = $active;
    }




    /**
     * Crea o actualiza un usuario.
     * @return bool true si se actualizó, false si se añadió
     */
    static function createUser($correo, $contraseña, $rol): bool
    {
        try {
            $user = new User($correo, $contraseña, $rol);
            $file = $_ENV['FILE_USERS_JSON']; // Ruta del fichero JSON
            $isSave = IoUserJson::saveJson($file, $user);
            return $isSave;
        } catch (IoException $e) {
            throw new Exception($e->getMessage(), $e->gethttpCode());
        }
    }

    /**
     * Obtiene una lista de usuarios.
     * 
     * @return User[] Array de objetos User
     */
    static function obtenerUsuarios(): array
    {
        try {
            $file = $_ENV['FILE_USERS_JSON']; // Ruta del fichero JSON
            $users = IoUserJson::readJson($file);
            return $users;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    /**
     * Obtiene un usuario.
     * 
     * @return User usuario con el correo y contraseña
     */
    static function obtenerUsuario($correo): ?User
    {
        try {
            $file = $_ENV['FILE_USERS_JSON']; // Ruta del fichero JSON
            $users = IoUserJson::readJson($file);
            $user = null;
            // buscar el usuario con el correo en el array
            foreach ($users as $key => $value) {
                if ($value->correo == $correo) {
                    $user = $value;
                    break;
                }
            }
            return $user;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    static function removeCount($correo): bool
    {
        try {
            $file = $_ENV['FILE_USERS_JSON']; // Ruta del fichero JSON
            $users = IoUserJson::readJson($file);
            $eliminado = false;
            // buscar el usuario con el correo en el array
            foreach ($users as $key => $value) {
                if ($value->correo == $correo) {
                    unset($users[$key]);
                    $eliminado = true;
                    break;
                }
            }
            if ($eliminado == false) {
                throw new Exception("El usuario no existe", 400);
            }
            // eliminar el usuario del array
            return IoUserJson::updatelistJson($file, $users);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }

    static function cambiarEstadoUsuario($correo, $estado = 0): string
    {
        try {
            $file = $_ENV['FILE_USERS_JSON']; // Ruta del fichero JSON
            $user = self::obtenerUsuario($correo);

            if ($user == null) {
                throw new Exception("El usuario no existe", 400);
            }

            $user->active = $estado;

            // si se encontró el usuario, actualizar el fichero
            $isUpdate = IoUserJson::updateJson($file, $user);

            if (!$isUpdate) {
                throw new Exception("No se pudo actualizar el usuario", 500);
            }

            return $estado == 1 ? "Usuario activado" : "Usuario desactivado";

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }
    }



    // static function createUser($correo, $contrasena, $rol){
//     $db = new DatabaseIntranet();
//     try {
//         $conn = $db->openConnection();
//         $stmt = $conn->prepare("INSERT INTO tb_usuarios (correo, contrasena, rol) VALUES (?, ?, ?)");
//         $stmt->bind_param("sss", $correo, $contrasena, $rol);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         echo $result->num_rows;
//     } catch (Exception $e) {
//         echo 'Excepción capturada: ', $e->getMessage(), "\n";
//     } finally {
//         $db->closeConnection();
//     }
// }


}