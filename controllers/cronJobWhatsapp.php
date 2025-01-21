<?php
namespace ApiMegaplex\Controllers;

require_once 'connections/DatabaseIntranet.php';
use Exception;



class cronJobWhatsapp
{

    static function update($message, $cell): int
    {
        $db = $conn;
        try {
            $conn = $db->openConnection();

            // Ejemplo para una consulta UPDATE o DELETE
            $stmt = $conn->prepare("UPDATE tb_cronJobWhatsapp SET message=? WHERE cell=?");
            $stmt->bind_param("ss", $message, $cell);
            $stmt->execute();

            // Obtener el número de filas afectadas
            $affectedRows = $stmt->affected_rows;

            return $affectedRows; // Devuelve el número de filas afectadas
        } catch (Exception $e) {
            throw $e;
        } finally {
            $db->closeConnection();
        }
        return 0;
    }
    static function insert($message, $cell): bool
    {
        $db = $conn;
        try {
            $conn = $db->openConnection();
            $stmt = $conn->prepare("INSERT INTO tb_cronJobWhatsapp SET message=?, cell=?");
            $stmt->bind_param("ss", $message, $cell);
            $success = $stmt->execute(); // Esto devolverá true o false dependiendo del éxito de la ejecución
            return $success;
        } catch (Exception $e) {
            throw $e;
        } finally {
            $db->closeConnection();
        }
        return false;
    }



    static function createCronJob()
    {
        $app = \Slim\Slim::getInstance(); // Obtener instancia de Slim para manejar respuestas y errores
        try {

            $data = json_decode($app->request()->getBody());

            if (!validarCampo($data, 'message', 'el campo message es obligatorio')) {
                return; // Detiene la ejecución si hay un error de validación
            }
            if (!validarCampo($data, 'cell', 'el campo cell es obligatorio')) {
                return; // Detiene la ejecución si hay un error de validación
            }

            $message = $data->message;
            $cell = $data->cell;

            $isInsert = self::insert($message, $cell);
            $app->response()->status(201);
            $response = array('state' => $isInsert, 'message' => 'Cron Job creado correctamente');
            echo json_encode($response);
        } catch (Exception $e) {
            echo handle_error($app, $e, "Error al crear el cronJob", 500);
        }
    }


}