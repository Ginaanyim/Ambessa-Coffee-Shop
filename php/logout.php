<?php
//starta sessionen
require_once 'start_session.php';

//Rensa sessionen, ta bort sesssionsvariabler och sessionen helt och hållet
session_unset();  
session_destroy();

//Ta bort session-cookien 
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', '', time() - 3600, '/'); // Sätt cookien som har gått ut för att ta bort den
}

//Ta bort session_id cookie
if (isset($_COOKIE['session_id'])) {
    setcookie('session_id', '', time() - 3600, '/'); // Ta bort din session_id-cookie
}

//Omdirigering till login-sidan
header("Location: /login.html");
exit();

