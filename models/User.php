<?php
// cargar autoload
namespace ApiMegaplex\Models;

require_once __DIR__ . '/../vendor/autoload.php';
use ApiMegaplex\Connections\DatabaseIntranet;
use Exception;

class User
{
    public $usuario;
    public $id_usuario;
    public $cargo;

    public function __construct($usuario, $id_usuario, $cargo) {
        $this->usuario = $usuario;
        $this->id_usuario = $id_usuario;
        $this->cargo = $cargo;
    }

    static function getUser()
    {

        $db = new DatabaseIntranet();

        $id_usuario = 116;
        try {
            $conn = $db->openConnection();
            $stmt = $conn->prepare("SELECT usuario, id_usuario, cargo FROM tb_empleados WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            // echo $result->num_rows;
            echo $result->fetch_assoc()['usuario'];
        } catch (Exception $e) {
            echo 'Excepción capturada: ', $e->getMessage(), "\n";
        } finally {
            $db->closeConnection();
        }



    }

    static function getAllUsers() {
        $db = new DatabaseIntranet();
    
        try {
            $conn = $db->openConnection();
            $stmt = $conn->prepare("SELECT usuario, id_usuario, cargo FROM tb_empleados");
            $stmt->execute();
            $result = $stmt->get_result();
    
            $users = [];
            while ($row = $result->fetch_assoc()) {
                // $users[] = $row;
                // $users[] = (object) $row; // Convertir el array asociativo a objeto
                $user = new User($row['usuario'], $row['id_usuario'], $row['cargo']);
                $users[] = $user;
            }
            $result->free(); // Liberar el resultado
            return $users;
        } catch (Exception $e) {
            echo 'Excepción capturada: ', $e->getMessage(), "\n";
        } finally {
            $db->closeConnection();
        }
    }
    


}



$users= User::getAllUsers();
// echo json_encode($users);
echo $users[0]->cargo;

