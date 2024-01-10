<?php 

// Lista de dominios permitidos
$allowedDomains = ['http://localhost/www.api.megaplexstars.com', 'https://www.api.megaplexstars.com'];

// Obtiene el dominio de la solicitud entrante
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Verifica si el dominio de la solicitud está en la lista de dominios permitidos
if (in_array($origin, $allowedDomains)) {
    // Establece el encabezado para permitir la solicitud desde este dominio
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    // Opcional: Manejar el caso de un origen no permitido
    // Puede ser enviando un código de estado HTTP como 403 Forbidden
    // o simplemente no hacer nada, lo que resultará en que el navegador bloquee la solicitud
    header('HTTP/1.1 403 Forbidden');
}
