<?php



/**
 * Genera el cuerpo del JSON para la solicitud de pago.
 *
 * @param string $invoice Número de la factura.
 * @param string $description Descripción del pago.
 * @param int $paymentMethodId ID del método de pago.
 * @param string $currency Moneda utilizada (ejemplo: "COP").
 * @param int $value Valor del pago.
 * @param bool $sandboxIsActive Si el entorno de pruebas está activo.
 * @param string $sandboxStatus Estado simulado del pago (ejemplo: "Approved").
 * @param string $urlResponse URL a redirigir después del pago.
 * @param string $urlConfirmation URL para confirmar la transacción.
 * @param string $methodConfirmation Método HTTP para la confirmación (ejemplo: "POST").
 * @param string $docType Tipo de documento del cliente (ejemplo: "CC").
 * @param string $document Número de documento del cliente.
 * @param string $authentication Información de autenticación adicional.
 * @return string JSON codificado con los datos proporcionados.
 */
function createPaymentRequestBody(
    string $invoice,
    string $description,
    int $paymentMethodId,
    string $currency,
    int $value,
    bool $sandboxIsActive,
    string $sandboxStatus,
    string $urlResponse,
    string $urlConfirmation,
    string $methodConfirmation,
    string $docType,
    string $document,
    string $authentication = ''
): string {
    // Validar campos obligatorios
    $fields = compact(
        'invoice',
        'description',
        'paymentMethodId',
        'currency',
        'value',
        'sandboxIsActive',
        'sandboxStatus',
        'urlResponse',
        'urlConfirmation',
        'methodConfirmation',
        'docType',
        'document',
        'authentication'
    );

    // Construir el array final
    $data = [
        "invoice" => $invoice,
        "description" => $description,
        "paymentMethod" => [
            "paymentMethodId" => $paymentMethodId
        ],
        "currency" => $currency,
        "value" => $value,
        "sandbox" => [
            "isActive" => $sandboxIsActive,
            "status" => $sandboxStatus
        ],
        "urlResponse" => $urlResponse,
        "urlConfirmation" => $urlConfirmation,
        "methodConfirmation" => $methodConfirmation,
        "client" => [
            "docType" => $docType,
            "document" => $document
        ],
        "extraData" => [
            "Authentication" => $authentication
        ]
    ];

    return json_encode($data, JSON_THROW_ON_ERROR);
}