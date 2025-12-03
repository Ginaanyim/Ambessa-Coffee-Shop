<?php
//inkluderar PHPMailer-biblioteket och skapar ett nytt PHPMailer-objekt
require 'PHPMailerAutoload.php';
$mail = new PHPMailer;

//Kontroll om formuläret skickades via post + hämta formulärets innehåll
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $fromEmail = $_POST['email'];
    $fromName = $_POST['name'];

    //SMTP-inställningar för att skicka via (gmail)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'virginia.anyim1@gmail.com';
    $mail->Password = 'rvdr sult nwjm vjuy';
    $mail->SMTPSecure = 'tls';

    //Sätter avsändare och mottagare
    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($to);

    //Filbilaga läggs till om en fil har laddats upp via formuläret
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $mail->addAttachment($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    }

    //Ämne och meddelande
    $mail->Subject = $subject;
    $mail->Body = $message . "\n\n This message is sent from a contact form!";

    //Skicka e-post, om lyckat skicka användaren tillbaka till formulär annars...
    if ($mail->send()) {
        header("Location: ../html/contact.html?success=1");
        exit();
    } else {
        //..skicka till formulärsidan med error
        header("Location: ../html/contact.html?error=1");
        exit();
    }
}
