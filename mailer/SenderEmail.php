<?php
namespace ApiMegaplex\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use ApiMegaplex\Exceptions\MailerException;

class SenderEmail
{

    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        // Configuración del servidor
        $this->mail->CharSet = 'UTF-8';
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['MAIL_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['MAIL_USERNAME'];
        $this->mail->Password = $_ENV['MAIL_PASSWORD'];
        $this->mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mail->Port = $_ENV['MAIL_PORT'];
    }

    public function sendTest()
    {
        try {

            // Remitente
            $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);

            // Destinatario
            $this->mail->addAddress('realpelee@gmail.com');

            // Contenido
            $this->mail->isHTML(true);
            // $this->mail->Subject = 'Asunto del correo';
            // $this->mail->Body    = 'Este es el cuerpo del mensaje en formato HTML.';
            // $this->mail->AltBody = 'Este es el cuerpo del mensaje para clientes de correo que no soportan HTML.';

            // Cargar el contenido del archivo HTML
            // Datos para el correo

            // $data = array("nombre" => "Steven");

            // Datos dinámicos
            $data = array(
                "nombre" => "Steven",
                "edad" => 42,
                "profesion" => "programador",
                "total" => "100.000"
            );


            $htmlContent = file_get_contents(__DIR__ . '/templates/notification.html');
            // Reemplazar el marcador con el valor real
            $htmlContent = str_replace('{{total}}', htmlspecialchars($data['total']), $htmlContent);

            // Reemplazar los marcadores con los valores reales
            // foreach ($data as $key => $value) {
            //     $htmlContent = str_replace("{{" . $key . "}}", htmlspecialchars($value), $htmlContent);
            // }


            $this->mail->Subject = 'prueba envío de correo';
            $this->mail->Body = $htmlContent;
            $this->mail->AltBody = 'Este es el cuerpo del mensaje para clientes de correo que no soportan HTML.';


            $this->mail->send();
            return 'Mensaje enviado correctamente';
        } catch (Exception $e) {
            throw new MailerException("El mensaje no pudo ser enviado. Error: " . $this->mail->ErrorInfo, 500);
        }

    }



}



