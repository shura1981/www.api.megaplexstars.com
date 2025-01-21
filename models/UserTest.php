<?php
namespace ApiMegaplex\Models;

require_once 'connections/DatabaseIntranet.php';


use Exception;
use JsonSerializable; // Add this import statement


class UserTest implements JsonSerializable
{
    public function jsonSerialize():array
    {
        return [
            'id' => $this->id,
            'usuario' => $this->usuario,
            'correo' => $this->correo,
            'celular' => $this->celular,
            'date_create_at' => $this->date_create_at,
            'active' => $this->active
        ];
    }

    public $id;
    public $usuario;
    public $correo;
    public $pass;
    public $celular;

    public $date_create_at;
    public $active;







    public function __construct($usuario, $correo, $celular, $pass, $date_create_at, $id = 0, $active = 1)
    {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->correo = $correo;
        $this->celular = $celular;
        $this->pass = $pass;
        $this->date_create_at = $date_create_at;
        $this->active = $active;
    }




    /**
     * Crea o actualiza un usuario.
     * @return bool true si se actualizó, false si se añadió
     */
    static function createUser($usuario, $correo, $celular, $pass, $date_create_at): bool
    {
        global $db;
        try {

            $stmt = $db->prepare("INSERT INTO tb_usuarios (usuario, correo, celular, pass, date_create_at) VALUES (?, ?, ?, ?, ?)");
            $encripPass = sha1($pass);
            $stmt->bind_param("sssss", $usuario, $correo, $celular, $encripPass, $date_create_at);
            $insert = $stmt->execute();
            return $insert;
        } catch (Exception $e) {
            throw $e;
        } finally {
            $stmt->close();
            $db->close();
        }
        return false;
    }

    /**
     * Obtiene una lista de usuarios.
     * 
     * @return UserTest[] Array de objetos UserTest
     */
    static function obtenerUsuarios(): array
    {
        $users = [];
        global $db;
        try {
            $stmt = $db->prepare("SELECT * FROM tb_usuarios");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $user = new UserTest($row['usuario'], $row['correo'], $row['celular'], $row['pass'], $row['date_create_at'], $row['id_usuario'], $row['active']);
                array_push($users, $user);
            }
            return $users;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        } finally {
            $stmt->free_result();
            $stmt->close();
            $db->close();
        }
        return $users;
    }


    /**
     * Obtiene un usuario.
     * 
     * @return UserTest usuario con el correo y contraseña
     */
    static function getUser($correo, $pass): ?UserTest
    {
        $user = null;
        global $conn;
        $stmt = null;
        try {
            $encripPass = sha1($pass);
            $stmt = $conn->prepare("SELECT * FROM tb_user_test WHERE correo = ? AND pass = ?");
            $stmt->bind_param("ss", $correo, $encripPass);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $user = new UserTest($row['usuario'], $row['correo'], $row['celular'], $row['pass'], $row['date_create_at'], $row['id'], $row['active']);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 500);
        } finally {
            if ($stmt != null) {
                $stmt->free_result();
                $stmt->close();
            }
            $conn->close();
        }

        return $user;
    }




}

// try {
//     $user = UserTest::getUser("realpelee@gmail.com", "1234");
//     echo json_encode($user);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }