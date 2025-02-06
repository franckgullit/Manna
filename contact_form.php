<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $prenom = htmlspecialchars(strip_tags(trim($_POST['prenom'])));
    $telephone = htmlspecialchars(strip_tags(trim($_POST['telephone'])));
    $subject = htmlspecialchars(strip_tags(trim($_POST["subject"])));
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));

    if (empty($prenom) || empty($telephone) || empty($subject) || empty($message)) {
        echo "Tous les champs sont obligatoires.";
        exit;
    }

    if (!preg_match("/^[0-9]{10}$/", $telephone)) {
        echo "Numéro de téléphone invalide.";
        exit;
    }

    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = getenv('MAIL_HOST'); 
        $mail->SMTPAuth = true;
        $mail->Username = getenv('MAIL_USERNAME'); 
        $mail->Password = getenv('MAIL_PASSWORD'); 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = getenv('MAIL_PORT');

        $to = getenv('MAIL_TO'); 
        $mail->setFrom(getenv('MAIL_FROM'), 'No Reply');
        $mail->addAddress($to);

        // Subject and Body
        $email_subject = "New Message from $prenom - $subject";
        $email_body = "Nom: $prenom\nTelephone: $telephone\nSubject: $subject\nMessage:\n$message";
        $mail->Subject = $email_subject;
        $mail->Body = $email_body;

        // Send email
        if ($mail->send()) {
            echo 'Votre message a été envoyé avec succès !';
        } else {
            echo 'Erreur lors de l\'envoi du message.';
        }

    } catch (Exception $e) {
        echo 'Erreur lors de l\'envoi du message: ' . $mail->ErrorInfo;
    }

} else {
    echo 'Accès non autorisé.';
}
?>
