<?php
session_start();

//Kollar om användare är inloggad via $_SESSION['user_email'] om inte skickar till login sida
if (!isset($_SESSION['user_email'])) {
    header("Location: ../html/login.html");
    exit();
}

//Inloggad, vidare till profilsidan
header("Location: ../html/profile.html");
exit();

