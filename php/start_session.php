<?php
//starta session eller fortsätter en påbörjad session
session_start();

//Kontroll om session-id redan finns, om inte skapa nytt id för sessionen
if (!isset($_SESSION['session_id'])) {
    session_regenerate_id(true);
    //sparar nya id:t i sessionen
    $_SESSION['session_id'] = session_id();
}

//Kollar om användaren har en cookie för session id
if (!isset($_COOKIE['session_id'])) {
    //skapa en cookie med session id som varar i 1 timme
    setcookie("session_id", $_SESSION['session_id'], time() + 3600, "/");
}
