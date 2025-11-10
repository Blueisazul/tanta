<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Incluir las clases necesarias de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Asegúrate de que el archivo 'PHPMailer/Exception.php' esté en la ruta correcta
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';

try {
    // Configuración de la conexión SMTP
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Cambia esto por tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'tanta.contacto@gmail.com';  // Tu correo de SMTP
    $mail->Password = 'bmdb gdrz bwzt sylz';  // La contraseña de tu correo
    $mail->SMTPSecure = 'tls'; //'ssl' 'tls';
    $mail->Port = 587; //465 587;

    // Receptor del correo
    $mail->setFrom('tanta.contacto@gmail.com', 'TANTA');
    $mail->addAddress($_POST['email']); // Correo del cliente

    // Contenido del correo
    $mail->isHTML(true);
    
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'compra';

    if ($tipo === 'estado') {
        // Mensaje para actualización de estado
        $estado = isset($_POST['estado']) ? htmlspecialchars($_POST['estado']) : 'actualizado';
        $id_pedido = isset($_POST['id_pedido']) ? htmlspecialchars($_POST['id_pedido']) : '';
        $mail->Subject = 'Actualización de estado de tu pedido';
        $mail->Body = '
            <h1>Actualización de Pedido</h1>
            <p><strong>N° Pedido:</strong> ' . $id_pedido . '</p>
            <p><strong>Nombre:</strong> ' . htmlspecialchars($_POST['nombre']) . '</p>
            <p><strong>Apellido:</strong> ' . htmlspecialchars($_POST['apellido']) . '</p>
            <p><strong>Correo:</strong> ' . htmlspecialchars($_POST['email']) . '</p>
            <p><strong>Mensaje:</strong> El estado de tu pedido ha cambiado a: <b>' . $estado . '</b>.</p>
        ';
    } else {
        // Mensaje de confirmación de compra (el que ya tienes)
         $id_pedido = isset($_POST['id_pedido']) ? htmlspecialchars($_POST['id_pedido']) : '';
        $mail->Subject = 'Confirmación de pedido';
        $mail->Body = '
            <h1>Se confirmó su pedido</h1>
            <p><strong>N° Pedido:</strong> ' . $id_pedido . '</p>
            <p><strong>Nombre:</strong> ' . htmlspecialchars($_POST['nombre']) . '</p>
            <p><strong>Apellido:</strong> ' . htmlspecialchars($_POST['apellido']) . '</p>
            <p><strong>Correo:</strong> ' . htmlspecialchars($_POST['email']) . '</p>
            <p><strong>Mensaje:</strong> ¡Gracias por tu compra, ' . htmlspecialchars($_POST['nombre']) . '! Tu pedido ha sido recibido.</p>
        ';
    }

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    // Enviar el correo
    $mail->send();
   echo json_encode(['success' => true, 'message' => 'Correo enviado correctamente']);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Hubo un error al enviar el mensaje. Error: {$mail->ErrorInfo}"]);
    exit;
}
?>