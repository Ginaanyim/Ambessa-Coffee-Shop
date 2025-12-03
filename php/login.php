<?php
//Koppla till databasen och starta sessionen
require_once 'db.php';
require_once 'start_session.php';

//Kontroll om formuläret skickades med post, tar bort ev mellanslag
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    //Hämtar användare från databasen med den skrivna eposten
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    //Om användare gittas med eposten och om..
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        //..lösenordet stämmer..
        if (password_verify($password, $user['password'])) {
            // Spara information om användaren i sessionen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstName'] = $user['firstName'];
            $_SESSION['lastName'] = $user['lastName'];
            $_SESSION['user_email'] = $user['email'];

            //Skapa ett nytt session-ID för säkerhet vid inloggning och kom till profilsidan
            session_regenerate_id(true);
            $_SESSION['session_id'] = session_id();
            header("Location: profile-replace.php");
            exit();
        } else {
            header("Location: ../html/login.html?error=password");
            exit();
        }
    } else {
        //Eposten finns inte i databasen, felmeddelande
        header("Location: ../html/login.html?error=email");
        exit();
    }
}

