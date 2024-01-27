<?php
namespace ApiMegaplex\Connections;
use mysqli;
class DatabaseIntranet
{
    private $host = "localhost";
    private $db_name = "crisenri_intranet";
    private $username = "crisenri_intranet";
    private $password = "].wKbv44W4LW8b";
    private $conn;

    public function openConnection():mysqli
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        $this->conn->set_charset("utf8mb4");

        if ($this->conn->connect_errno) {
            echo 'Conexión Fallida : ', $this->conn->connect_error;
            exit();
        }

        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn->close();
    }

    public function getEmployee($id_usuario)
    {
        $stmt = $this->conn->prepare("SELECT usuario, id_usuario, cargo FROM tb_empleados WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}

 
