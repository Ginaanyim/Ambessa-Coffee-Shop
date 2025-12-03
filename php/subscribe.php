<?php
include 'db.php'; 

//Konroll om formuläret har skickats med post
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    //Hämtar och trimmar e-post
    $email = trim($_POST["email"]);

    //Kontroll om eposten är giltig
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        //Kolla om epost redan finns i databasen
        $checkSql = "SELECT id FROM subscribers WHERE email = '$email'";
        $checkResult = $conn->query($checkSql);

         //Eposten finns redan = skicka tillbaka plus felmeddelande
        if ($checkResult->num_rows > 0) {
            header("Location: ../html/home.html?error=subscribed");
            exit;
        } else {
            //Annars lägg till nya eposten i databasen
            $insertSql = "INSERT INTO subscribers (email) VALUES ('$email')";
            if ($conn->query($insertSql)) {
                //Om det gick att lägga in den = skicka till thank-you sidan
                header("Location: ../html/thank-you.html");
                exit;
            } else {
                //Om något går fel = visa serverfel
                header("Location: ../html/home.html?error=server_error");
                exit;
            }
        }

    } else {
        //ogiltig epost = skicka tillbaka med felmeddelande
        header("Location: ../html/home.html?error=invalid_email");
        exit;
    }
}

// Stäng databaskopplingen
$conn->close();
