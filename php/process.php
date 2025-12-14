<?php
// Tvingar PHP att visa fel (bra att ha kvar)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Använder den ABSOLUTA sökvägen till filen i htdocs/
require '../.config/mail_config.php';

// inkluderar PHPMailer-biblioteket och skapar ett nytt PHPMailer-objekt
require 'PHPMailerAutoload.php';
$mail = new PHPMailer;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Hämta formulärdata
    $to = $smtp_to_default; 
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $fromEmail = $_POST['email']; 
    $fromName = $_POST['name'];   

    // --- STRATO SMTP-INSTÄLLNINGAR (Hämtas från mail_config.php) ---
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->Port = $smtp_port; 
    $mail->SMTPAuth = $smtp_auth; 
    $mail->SMTPSecure = $smtp_secure; 

    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password; 
    
    // Sätter Avsändaren TILL din Strato-adress (Krälvs för autentisering)
    $mail->setFrom($smtp_username, 'Kontaktformulär - ' . $fromName); 
    
    // Sätter Svar till (Reply-To) TILL kundens e-post
    $mail->addReplyTo($fromEmail, $fromName); 
    
    // Sätter Mottagaren (TO) till din Strato-adress
    $mail->addAddress($to); 

    // Filbilaga läggs till om en fil har laddats upp via formuläret
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $mail->addAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    }

    // Ämne och meddelande
    $mail->Subject = 'NYTT ÄRENDE: ' . $subject . ' (Från: ' . $fromName . ')';
    $mail->Body = $message . "\n\n--- Detta meddelande skickades från ambessa-store.com kontaktformulär.";

    // Skicka e-post, om lyckat omdirigera
    if ($mail->send()) {
        header("Location: ../contact.html?success=1");
        exit();
    } else {
        // Om fel, visa felmeddelandet
        echo "<h1>E-postfel</h1>";
        echo "<p>Kunde inte skicka meddelandet. Fel: " . $mail->ErrorInfo . "</p>";
    }
}
?>