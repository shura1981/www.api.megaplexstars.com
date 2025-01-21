<?php

namespace ApiMegaplex\Controllers;

require_once 'helpers/helpers.php';
require_once 'helpers/siste_credito/createPaymentRequestBody.php';

use ApiMegaplex\Io\IoResponse;
use Exception;

class ApiSisteCreditoController
{

  const NAME_BANK = "SISTECREDITO";

  static function query()
  {
    $app = \Slim\Slim::getInstance(); // Obtener instancia de Slim para manejar respuestas y errores
    $idTransaccion = $app->request()->params('idTransaccion');
    try {
      self::validarParametrosGet(['idTransaccion' => $idTransaccion]); // Validar parámetros de la solicitud
      $responseTransaccion = self::queryIdTransaction($idTransaccion); // Consultar id de transacción
      $estadoTransaccion = self::obtenerEstadoDeTransaccion($responseTransaccion['data']);
      echo json_encode($estadoTransaccion, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (Exception $e) {
      echo handle_error($app, $e); // Manejar excepción
    }
  }

  static function validarParametrosGet($fields)
  {
    $requiredFields = [
      'idTransaccion'
    ];
    validateFields($fields, $requiredFields);
  }

  static function create()
  {
    $app = \Slim\Slim::getInstance(); // Obtener instancia de Slim para manejar respuestas y errores
    $request = $app->request;
    $body = $request->getBody();
    $data =   json_decode($body, true); // Convertir el cuerpo de la solicitud a un array asociativo

    try {
      self::validarCamposPost($data); // Validar campos obligatorios
      $idTransaccion = self::genearateIdTransaction($data); // Generar id de transacción
      $responseTransaccion = self::queryIdTransaction($idTransaccion); // Consultar id de transacción
      $paymentRedirectUrl = self::obtenerUrlRedireccion($responseTransaccion['data']);

      if (empty($paymentRedirectUrl)) {
        $paymentRedirectUrl = self::reintentarObtenerUrlRedireccion($responseTransaccion['data']);
      }

      echo json_encode(["idTransaccion" => $idTransaccion, "paymentRedirectUrl" => $paymentRedirectUrl], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } catch (Exception $e) {
      echo handle_error($app, $e); // Manejar excepción
    }
  }

  /**
   * Reintenta obtener la url de redirección de la transacción.
   * @param array $responseTransaccion Respuesta de la transacción.
   * @return string url de redirección.
   * @throws Exception Si ocurre un error al obtener la url de redirección.
   */
  static function reintentarObtenerUrlRedireccion($responseTransaccion)
  {
    sleep(5); // Esperar 5 segundos antes de reintentar
    $estadoTransaccion = self::obtenerEstadoDeTransaccion($responseTransaccion);
    $paymentRedirectUrl = $estadoTransaccion['urlPayment'];
    if (empty($paymentRedirectUrl)) {
      IoResponse::responseSave($responseTransaccion);
      throw new Exception("Error al obtener la url de redirección");
    }
    return $paymentRedirectUrl;
  }

  static function validarCamposPost($fields)
  {
    $requiredFields = [
      'invoice',
      'description',
      'value',
      'docType',
      'document',
      'mode'
    ];
    validateFields($fields, $requiredFields);
  }

  /**
   * Genera un id de transacción de pago.
   * @param array{
   * string invoice,
   * string description,
   * float value,
   * string docType,
   * string document,
   * string mode
   * } $data
   * @throws Exception Si ocurre un error al generar el id de la transacción.
   * @return string id de la transacción.
   */
  static function genearateIdTransaction($data)
  {
    //Obtener variables de entorno
    $URL_POST_CREATE_ORDER = $_ENV['URL_POST_CREATE_ORDER'];
    $TIMEOUT_CURL_TOKEN = $_ENV['TIMEOUT_CURL_TOKEN']; // tiempo de espera de conexión y respuesta en segundos
    $URL_RESPONSE_SISTECREDITO = $_ENV['URL_RESPONSE_SISTECREDITO'];
    $URL_CONFIRMATION_SITECREDITO = $_ENV['URL_CONFIRMATION_SITECREDITO'];
    $URL_RESPONSE_SISTECREDITO_LINKPAGO = $_ENV['URL_RESPONSE_SISTECREDITO_LINKPAGO'];

    $URL_RESPONSE = $data['mode'] == '1' ?  $URL_RESPONSE_SISTECREDITO : $URL_RESPONSE_SISTECREDITO_LINKPAGO;

    $requestBody = createPaymentRequestBody(
      $data['invoice'],
      $data['description'],
      2,
      "COP",
      $data['value'],
      false,
      "Approved",
      $URL_RESPONSE,
      $URL_CONFIRMATION_SITECREDITO,
      "POST",
      $data['docType'],
      $data['document']
    );

    // Configuración de la solicitud cURL
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => $URL_POST_CREATE_ORDER,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => $TIMEOUT_CURL_TOKEN, // tiempo de espera de conexión y respuesta en segundos
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $requestBody,
      CURLOPT_HTTPHEADER => self::obtenerHeaders(),
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response, true);
    $idTransaccion = self::obtenerId($response['data']);

    if ($idTransaccion == null) {
      IoResponse::responseSave($response);
      throw new Exception("Error al obtener el id de la transacción");
    }

    return $idTransaccion;
  }

  /**
   * Consulta un id de transacción de pago.
   * @param string $idTransaccion
   * @return array Respuesta de la consulta.
   */
  static function queryIdTransaction($idTransaccion)
  {
    //Obtener variables de entorno
    $URL_GET_QUERY_TRANSACTION = $_ENV['URL_GET_QUERY_TRANSACTION'] . "?transactionId=" . $idTransaccion;
    $TIMEOUT_CURL_TOKEN = $_ENV['TIMEOUT_CURL_TOKEN']; // tiempo de espera de conexión y respuesta en segundos

    // Configuración de la solicitud cURL
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => $URL_GET_QUERY_TRANSACTION,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => $TIMEOUT_CURL_TOKEN, // tiempo de espera de conexión y respuesta en segundos
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => self::obtenerHeaders(),
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($response, true);
    return $response;
  }

  static function obtenerHeaders()
  {
    $SUBSCRIPTION_KEY = $_ENV['SUBSCRIPTION_KEY'];
    $APPLICATION_KEY = $_ENV['APPLICATION_KEY'];
    $APPLICATION_TOKEN = $_ENV['APPLICATION_TOKEN'];

    return [
      'SCOrigen: Production',
      'country: co',
      'Content-Type: application/json',
      'Ocp-Apim-Subscription-Key:' . $SUBSCRIPTION_KEY,
      'ApplicationKey:' . $APPLICATION_KEY,
      'ApplicationToken:' . $APPLICATION_TOKEN,
      'SCLocation: 0,0'
    ];
  }

  /**
   * Estructura del campo data de la respuesta de la API de SisteCredito.
   * @param array{
   * _id: string,
   * paymentMethodResponse: array{
   * transactionId:string,
   * statusResponse:string,
   * description:string,
   * codeResponse:string,
   * }
   * } $data
   * @return string|null id de la transacción.
   */
  static function obtenerId($data)
  {
    if (empty($data) || empty($data['_id'])) {
      return null;
    }
    return $data['_id'];
  }

  static function obtenerEstadoDeTransaccion($data)
  {

    if (empty($data) || empty($data['paymentMethodResponse'])) {
      IoResponse::responseSave($data);
      throw new Exception("Error al obtener los datos de la transacción");
    }

    $paymentMethodResponse = $data['paymentMethodResponse'];
    $invoice = $data['invoice'] ?? null;
    $creationDate = $data['creationDate'] ?? null;
    $creationDate = str_replace('T', ' ', $creationDate);
    $currency = $data['currency'] ?? null;
    $value = $data['value'] ?? 0;
    $statusResponse = $paymentMethodResponse['statusResponse'] ?? null;
    $transactionId = $paymentMethodResponse['transactionId'] ?? null;
    $approvalCode = $paymentMethodResponse['approvalCode'] ?? null;
    $urlPayment = $paymentMethodResponse['paymentRedirectUrl'] ?? null;

    $status = self::normalizarRespuestaTransaccion($statusResponse);

    $x_cod_response =  $status['code'];
    $x_transaction_date = $creationDate;
    $x_response = $status['response'];
    $x_response_reason_text = $status['reason'];
    $x_transaction_id = $transactionId;
    $x_bank_name = self::NAME_BANK;
    $x_approval_code = $approvalCode;
    $x_id_invoice = $invoice;
    $x_amount =  $value;
    $x_currency_code = $currency;

    return [
      "data" => [
        "x_cod_response" => $x_cod_response,
        "x_transaction_date" => $x_transaction_date,
        "x_response" => $x_response,
        "x_response_reason_text" => $x_response_reason_text,
        "x_transaction_id" => $x_transaction_id,
        "x_bank_name" => $x_bank_name,
        "x_approval_code" => $x_approval_code,
        "x_id_invoice" => $x_id_invoice,
        "x_amount" => $x_amount,
        "x_currency_code" => $x_currency_code,
        "urlPayment" => $urlPayment
      ]
    ];
  }

  /**
   * Estructura del campo data de la respuesta de la API de SisteCredito.
   * @param array{
   * _id: string,
   * invoice:string,
   * storeId:string,
   * vendorId:string,
   * description:string,
   * creationDate:string,
   * transactionStatus:string,
   * currency:string,
   * value:float,
   * paymentMethodResponse: array{
   * paymentRedirectUrl:string,
   * transactionId:string,
   * driverId:string,
   * statusResponse:string,
   * codeResponse:string,
   * description:string,
   * authorizationCode:string,
   * approvalCode:string,
   * }
   * } $data
   * @return string|null url de redirección.
   */
  static function obtenerUrlRedireccion($data)
  {
    if (empty($data) || empty($data['paymentMethodResponse'])) {
      IoResponse::responseSave($data);
      throw new Exception("Error al obtener la url de redirección");
    }

    $paymentMethodResponse = $data['paymentMethodResponse'];
    $paymentRedirectUrl = $paymentMethodResponse['paymentRedirectUrl'] ?? null;
    return $paymentRedirectUrl;
  }

  /**
   * Normaliza la respuesta de la transacción.
   * @param string $statusResponse
   * @return array{
   * response: string,
   * code: int,
   * reason: string
   * }
   */
  static function normalizarRespuestaTransaccion($statusResponse)
  {
    // Mapeo de respuestas por status
    $statusMap = [
      "Approved" => [
        'response' => 'APROBADA',
        'code' => 1,
        'reason' => "El valor solicitado por el cliente para cubrir la compra se aprobó en un 100%"
      ],
      "Rejected" => [
        'response' => 'PAGO RECHAZADO',
        'code' => 2,
        'reason' => "La aplicación de crédito en línea es rechazada. El cliente no ha sido aprobado para obtener un crédito con SISTECREDITO."
      ],
      "Pending" => [
        'response' => 'PENDIENTE POR CR',
        'code' => 3,
        'reason' => "La aplicación de crédito en línea se encuentra en proceso de validación por parte de la plataforma SISTECREDITO"
      ],
      "Failed" => [
        'response' => 'ERROR CR',
        'code' => 4,
        'reason' => "Ha sucedido un error en la plataforma de SISTECREDITO. El cliente debe ser redirigido a seleccionar un método de pago diferente a SISTECREDITO en el e-commerce"
      ],
      "PendingForPaymentMethod" => [
        'response' => 'PENDIENTE POR MÉTODO DE PAGO',
        'code' => 10,
        'reason' => "La aplicación de crédito en línea se encuentra en proceso de validación por parte de la plataforma SISTECREDITO"
      ],
      "Cancelled" => [
        'response' => 'Cancelada',
        'code' => 11,
        'reason' => "La aplicación de crédito en línea es declinada por el cliente"
      ],
    ];

    // Verificar si el statusResponse existe en el mapeo
    if (isset($statusMap[$statusResponse])) {
      return $statusMap[$statusResponse];
    }

    // Respuesta predeterminada si no se encuentra el statusResponse
    return [
      'response' => 'DESCONOCIDO',
      'code' => 0,
      'reason' => "El estado de la transacción no es reconocido."
    ];
  }
}
