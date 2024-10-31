<?php
date_default_timezone_set('America/Bogota');
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
function sendWhatsapp($number, $message, $port, $isMultiMedia = false)
{

    if ($isMultiMedia) {
        $url =  str_replace(
            ['{port}'],
            [$port],
            $message
        );
    } else {
        $url = "https://elitenutapp.com/whatsapp/message?to=57$number&message=$message&port=$port";
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
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
    $query = "UPDATE tb_cronjobwhatsapp SET  time_update='$timeSpan' WHERE id= $id";
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
        $query = "DELETE FROM tb_cronjobwhatsapp WHERE id= $id";
        $conn = getConnection();
        return $conn->query($query);
    } catch (Exception $e) {
        throw $e;
    } finally {
        closeConnection($conn);
    }
}


/**
 * Obtener los registros de la tabla tb_cronjobwhatsapp
 * @return array{
 * id: int,
 * message: string,
 * time_create: string,
 * time_update: string,
 * cell: string,
 * multimedia: boolean
 * }[]
 * @throws Exception
 */
function getList()
{
    try {
        $query = "SELECT id,message, time_create, time_update, cell, multimedia FROM tb_cronjobwhatsapp WHERE time_update IS  NULL LIMIT 50;";
        $conn = getConnection();
        $rows = [];
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            // casting de multimedia a boolean
            $row['multimedia'] = (bool) $row['multimedia'];
            $rows[] = $row;
        }
        return $rows;
    } catch (Exception $e) {
        throw $e;
    } finally {
        closeConnection($conn);
    }
}

function alternarPuerto()
{
    $stateFile = "../public/files/lastport.file"; // Ruta al archivo de estado del puerto

    // Leer el último puerto almacenado desde el archivo
    $ultimoPuerto = file_exists($stateFile) ? file_get_contents($stateFile) : null;
    $port1 = 8086;
    $port2 = 8085;

    // Alternar entre $port1 y $port2
    if ($ultimoPuerto === (string) $port1) {
        $ultimoPuerto = $port2;
    } else {
        $ultimoPuerto = $port1;
    }

    // Guardar el último puerto utilizado en el archivo
    file_put_contents($stateFile, (string) $ultimoPuerto);

    return $ultimoPuerto;
}


function checkProcess()
{
    $stateFile = "../public/files/state.file"; // Ruta al archivo de estado

    // Verificar si el proceso está OCUPADO
    if (file_get_contents($stateFile) === "OCUPADO") {
        return; // Salir si ya está ocupado
    }

    // Establecer el estado a OCUPADO
    file_put_contents($stateFile, "OCUPADO");

    $timePause = 20;
    $list = getList();

    if (count($list) == 0) {
        echo "No hay registros para procesar";
        file_put_contents($stateFile, "LIBRE"); // Establecer el estado a LIBRE
        return;
    }

    foreach ($list as $item) {
        $id = $item['id'];
        $message = $item['message'];
        $cell = $item['cell'];
        $isMultiMedia = $item['multimedia'];
        $port = alternarPuerto();
        sendWhatsapp($cell, $message, $port, $isMultiMedia);
        notification($message);
        update($id);
        sleep($timePause); // pausar el proceso por $timePause segundos
    }

    echo "Tarea ejecutada";
    file_put_contents($stateFile, "LIBRE"); // Establecer el estado a LIBRE al final
}

checkProcess();

 
