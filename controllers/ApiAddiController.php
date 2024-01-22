<?php
namespace ApiMegaplex\Controllers;

use Exception;

class ApiAddiController
{

    static function obtenerToken()
    {
        try {
            $app = \Slim\Slim::getInstance(); // Obtener instancia de Slim para manejar respuestas y errores

            //Obtener variables de entorno
            $ADDI_AUDIENCE = $_ENV['ADDI_AUDIENCE'];
            $GRANT_TYPE = $_ENV['GRANT_TYPE'];
            $CLIENT_ID = $_ENV['CLIENT_ID'];
            $CLIENT_SECRET = $_ENV['CLIENT_SECRET'];
            $URL_GET_TOKEN = $_ENV['URL_GET_TOKEN'];

            $TIMEOUT_CURL_TOKEN = $_ENV['TIMEOUT_CURL_TOKEN']; // tiempo de espera de conexión y respuesta en segundos

            // crear un array con datos POST a enviar
            $data = array(
                "audience" => $ADDI_AUDIENCE,
                "grant_type" => $GRANT_TYPE,
                "client_id" => $CLIENT_ID,
                "client_secret" => $CLIENT_SECRET
            );

            // Codificar $data en JSON
            $jsonData = json_encode($data);

            //Crear cabecera HTTP
            // $authorizationToken = 'tu_token_aquí'; // Asegúrate de obtener este valor de manera segura
            // $headers = array(
            //     'Content-Type: application/json',
            //     'Authorization: Bearer ' . $authorizationToken
            // );
            $headers = array('Content-Type: application/json');

            // Inicializar cURL
            $ch = curl_init($URL_GET_TOKEN);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            // Enviar datos como JSON
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            // Establecer el encabezado Content-Type en application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT_CURL_TOKEN); // tiempo de espera de conexión en segundos
            curl_setopt($ch, CURLOPT_TIMEOUT, $TIMEOUT_CURL_TOKEN); // tiempo de espera de ejecución en segundos
            $response = curl_exec($ch); // Obtener respuesta
            if ($response === false) {
                $error = curl_error($ch);
                $errorCode = curl_errno($ch);
                curl_close($ch); // Siempre cerrar cURL, incluso si hay un error
                // Puedes manejar el error como prefieras, aquí un ejemplo:
                $app->response()->status(500); // Error interno del servidor
                echo json_encode(["error" => "Error en cURL: $error (Código: $errorCode)", "message" => "Error al obtener el token"]);
                return;
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtener el código de la respuesta
            curl_close($ch); // cerrar cURL
            $response = json_decode($response, true); // Decodificar respuesta JSON
            $app->response()->status($httpcode); // Establecer código de respuesta
            echo json_encode($response); // Enviar respuesta JSON
        } catch (Exception $e) {
            echo handle_error($app, $e); // Manejar excepción
        }
    }

    static function obtenerUrlRedireccion()
    {

        try {
            $app = \Slim\Slim::getInstance(); // Obtener instancia de Slim para manejar respuestas y errores

            $dataRequest = json_decode($app->request()->getBody());

            // if (!validarCampo($dataRequest, 'tokenAddi', 'el campo tokenAddi es obligatorio')) {
            //     return; // Detiene la ejecución si hay un error de validación
            // }

            // Codificar $data en JSON
            // $data = self::obtenerDatosDelPedido();
            $data = $dataRequest;
            $jsonData = json_encode($data);

            $TIMEOUT_CURL_TOKEN = $_ENV['TIMEOUT_CURL_TOKEN']; // tiempo de espera de conexión y respuesta en segundos
            $URL_GET_REDIRECTION = $_ENV['URL_GET_REDIRECTION'];



            //Crear cabecera HTTP
            $authorizationToken = $dataRequest->tokenAddi; // Asegúrate de obtener este valor de manera segura
            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $authorizationToken
            );

            // Inicializar cURL
            $ch = curl_init($URL_GET_REDIRECTION);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            // Enviar datos como JSON
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            // Establecer el encabezado Content-Type en application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT_CURL_TOKEN); // tiempo de espera de conexión en segundos
            curl_setopt($ch, CURLOPT_TIMEOUT, $TIMEOUT_CURL_TOKEN); // tiempo de espera de ejecución en segundos
            $response = curl_exec($ch); // Obtener respuesta
            if ($response === false) {
                $error = curl_error($ch);
                $errorCode = curl_errno($ch);
                curl_close($ch); // Siempre cerrar cURL, incluso si hay un error
                // Puedes manejar el error como prefieras, aquí un ejemplo:
                $app->response()->status(500); // Error interno del servidor
                echo json_encode(["error" => "Error en cURL: $error (Código: $errorCode)", "message" => "Error al obtener el token"]);
                return;
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtener el código de la respuesta

            // obtener el campo Location de la cabecera de respuesta de $response
            $location = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

            curl_close($ch); // cerrar cURL



            $response = array("url" => $location);


            $app->response()->status($httpcode); // Establecer código de respuesta
            echo json_encode($response); // Enviar respuesta JSON

        } catch (Exception $e) {
            echo handle_error($app, $e); // Manejar excepción
        }


    }


    static function obtenerDatosDelPedido()
    {

        $jsonString = '{
            "orderId": "653449",
            "totalAmount": "255000.0",
            "shippingAmount": "50000.0",
            "totalTaxesAmount": "100000.0",
            "currency": "COP",
            "items": [
              {
                "sku": "PD-122354",
                "name": "protein chcolate",
                "quantity": "5",
                "unitPrice": 9950,
                "tax": 0,
                "pictureUrl": "https://picture.example.com/?img=test",
                "category": "technology",
                "brand": "adidas"
              }
            ],
            "client": {
              "idType": "CC",
              "idNumber": "6394880",
              "firstName": "STEVEN",
              "lastName": "REALPE",
              "email": "realpelee@gmail.com",
              "cellphone": "3044937753",
              "cellphoneCountryCode": "+57",
              "address": {
                "lineOne": "cr 48 156 25 25",
                "city": "Bogotá D.C",
                "country": "CO"
              }
            },
            "shippingAddress": {
              "lineOne": "cr 48 156 25 25",
              "city": "Bogotá D.C",
              "country": "CO"
            },
            "billingAddress": {
              "lineOne": "cr 48 156 25 25",
              "city": "Bogotá D.C",
              "country": "CO"
            },
            "pickUpAddress": {
              "lineOne": "cr 48 156 25 25",
              "city": "Bogotá D.C",
              "country": "CO"
            },
            "allyUrlRedirection": {
              "logoUrl": "https://picture.example.com/?img=test",
              "callbackUrl": "https://www.nutramerican.com/api_MegaplexStar/api/confirmation_addi.php",
              "redirectionUrl": "https://nutramerican.com/ecommerceNutra/response-addy/index.html?id_order=653449"
            },
            "geoLocation": {
              "latitude": "4.624335",
              "longitude": "-74.063644"
            }
          }';

        return json_decode($jsonString, true);

    }

}
