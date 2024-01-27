<?php
function consumePutApi($url, $data, $enabledSSL = false)
{
    $ch = curl_init();
    // Establecer la URL y otras opciones necesarias
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $enabledSSL); // Desactivar verificación SSL
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    // Ejecutar la petición y obtener la respuesta
    $response = curl_exec($ch);

    // Comprobar si ocurrió algún error
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("Error en la petición: " . $error);
    }
    // Cerrar la conexión cURL
    curl_close($ch);
    return json_decode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
}
function notification($message)
{
    $hoy = date('Y-m-d H:i:s');
    $package = array("fecha" => $hoy, "message" => $message);
    $url = "https://elitenutritiongroup-9385a.firebaseio.com/cronJob.json";
    consumePutApi($url, $package);
}
function sendWhatsapp($number, $message, $port)
{
    $url = "https://elitenutapp.com/whatsapp/message?to=57$number&message=$message&port=$port";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 20);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $response = curl_exec($curl);
    $data = json_decode($response);
    curl_close($curl);
    return $data;
}

function getConnection()
{
    $mysqlElite = new mysqli("localhost", "crisenri_intranet", "].wKbv44W4LW8b", "crisenri_intranet");
    $mysqlElite->set_charset("utf8mb4");
    if (mysqli_connect_errno()) {
        echo 'Conexión Fallida : ', mysqli_connect_error();
        exit();
    }
    return $mysqlElite;
}

function closeConnection($conn)
{
    if ($conn != null)
        $conn->close();

}
function update($id)
{
    //obtener la fecha actual y hora yyyy-mm-dd HH:mm
    $timeSpan = date('Y-m-d H:i:s');
    $query = "UPDATE tb_cronJobWhatsapp SET  time_update='$timeSpan' WHERE id= $id";
    try {
        $conn = getConnection();
        return $conn->query($query);
    } catch (Exception $e) {
        throw $e;
    } finally {
        closeConnection($conn);
    }

}

function delete($id)
{

    try {
        $query = "DELETE FROM tb_cronJobWhatsapp WHERE id= $id";
        $conn = getConnection();
        return $conn->query($query);
    } catch (Exception $e) {
        throw $e;
    } finally {
        closeConnection($conn);
    }

}

function getList()
{
    try {
        $query = "SELECT id,message, time_create, time_update, cell FROM tb_cronJobWhatsapp WHERE time_update IS  NULL;";
        $conn = getConnection();
        $rows = [];
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    } catch (Exception $e) {
        throw $e;
    } finally {
        closeConnection($conn);
    }

}

function checkProcess()
{

    $timePause = 10;


    $list = getList();
    if (count($list) == 0) {
        echo "No hay registros para procesar";
        return;
    }
    $i = 0; // Inicializar el contador
    foreach ($list as $item) {
        $id = $item['id'];
        $message = $item['message'];
        $cell = $item['cell'];
        $port = $i % 2 == 0 ? 8086 : 8085;
        sendWhatsapp($cell, $message, $port);
        notification($message);
        update($id);
        sleep($timePause); // pausar el proceso por 5 segundos
        $i++;
    }

    echo "Tarea ejecutada";
}



checkProcess();
