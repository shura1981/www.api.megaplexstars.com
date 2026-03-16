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

function createCampaignTemplateContextBody($fullName, $idOrder, $mount): array
{
    $fullName = (string) $fullName;
    $idOrder = (string) $idOrder;
    $mount = (string) $mount;

    return [
        "to" => "{{phoneNumberTemplateContext}}",
        "templateName" => "confirmacion_pedido",
        "languageCode" => "es_CO",
        "components" => [
            [
                "type" => "header",
                "parameters" => [
                    [
                        "type" => "image",
                        "image" => [
                            "link" => "https://nutramerican.com/img/logos/nutramerican-pharma.jpg"
                        ]
                    ]
                ]
            ],
            [
                "type" => "body",
                "parameters" => [
                    [
                        "type" => "text",
                        "text" => $fullName
                    ],
                    [
                        "type" => "text",
                        "text" => $idOrder
                    ],
                    [
                        "type" => "text",
                        "text" => $mount
                    ]
                ]
            ]
        ],
        "agentId" => 1,
        "idSession" => "{{phoneNumberTemplateContext}}",
        "texto_cliente" => "",
        "texto_agente" => "Hola *$fullName* te escribimos de nutramerican.com. Tu pedido con el numero de pago *N $idOrder* por valor de *$mount* ha sido *APROBADO* y sera entregado lo mas pronto posible.",
        "active" => 1
    ];
}

function sendWhatsappCampaignTemplateContext(array $payload)
{
    $url = "https://crm.elitenutapp.com/api/whatsapp/send/campaign-template-context";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($curl);
    if ($response === false) {
        $error = curl_error($curl);
        curl_close($curl);
        throw new Exception("Error en envio de campana WhatsApp: " . $error);
    }

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        "httpCode" => $httpCode,
        "data" => json_decode($response, true),
        "raw" => $response,
    ];
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
function updateV2($id)
{
    $timeSpan = date('Y-m-d H:i:s');
    $query = "UPDATE tb_cronjobwhatsapp_v2 SET time_update='$timeSpan' WHERE id= $id";
    try {
        $conn = getConnection();
        return $conn->query($query);
    } catch (Exception $e) {
        throw $e;
    } finally {
        closeConnection($conn);
    }
}


/**
 * Obtener los registros de la tabla tb_cronjobwhatsapp_v2
 * @return array{
 * id: int,
 * id_order: string,
 * full_name: string,
 * amount: string,
 * number_cell: string,
 * time_create: string,
 * time_update: ?string
 * }[]
 * @throws Exception
 */
function getListV2()
{
    try {
        $query = "SELECT id, id_order, full_name, amount, number_cell, time_create, time_update FROM tb_cronjobwhatsapp_v2 WHERE time_update IS NULL LIMIT 50;";
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

function processWhatsappCronjobV2($timePause = 3)
{
    $list = getListV2();

    if (count($list) == 0) {
        echo "No hay registros para procesar";
        return;
    }

    foreach ($list as $item) {
        $id = $item['id'];
        $idOrder = $item['id_order'];
        $fullName = $item['full_name'];
        $amount = $item['amount'];
        $numberCell = "57" . $item['number_cell'];

        $payload = createCampaignTemplateContextBody($fullName, $idOrder, $amount);
        $payload['to'] = $numberCell;
        $payload['idSession'] = $numberCell;
        sendWhatsappCampaignTemplateContext($payload);
        updateV2($id);

        sleep($timePause);
    }
}

function checkProcess()
{
    $stateFile = __DIR__ . "/../public/files/state.file"; // Ruta absoluta del archivo de estado
    $stateDir = dirname($stateFile);

    if (!is_dir($stateDir)) {
        mkdir($stateDir, 0777, true);
    }

    if (!file_exists($stateFile)) {
        file_put_contents($stateFile, "LIBRE");
    }

    // Verificar si el proceso está OCUPADO
    if (trim((string) file_get_contents($stateFile)) === "OCUPADO") {
        return; // Salir si ya está ocupado
    }

    // Establecer el estado a OCUPADO
    file_put_contents($stateFile, "OCUPADO");

    try {
        processWhatsappCronjobV2(3);
        echo "Tarea ejecutada";
    } finally {
        file_put_contents($stateFile, "LIBRE"); // Establecer el estado a LIBRE al final
    }
}

checkProcess();
