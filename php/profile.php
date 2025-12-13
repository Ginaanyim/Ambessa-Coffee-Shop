<?php
session_start();

// Kontroll om användare är inloggad annars vidare till login sida
if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.html");
    exit();
}
// Inloggad, vidare till profilsidan
header("Location: ../profile.html");
exit();