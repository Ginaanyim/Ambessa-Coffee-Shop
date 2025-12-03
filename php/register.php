<?php
include 'db.php';

//Kontroll om formuläret skickades med post + hämta värden från formuläret 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    //Kontroll om lösenordet matchar bekräftelsen annars felmeddelande
    if ($password !== $confirm) {
        header("Location: ../html/register.html?error=password_mismatch");
        exit();
    }

    //Kolla om email redan finns i databasen om den finns skicka användare till login sida med felmeddelande
    $checkSql = "SELECT id FROM users WHERE email = '$email'";
    $checkResult = $conn->query($checkSql);
    if ($checkResult->num_rows > 0) {
        header("Location: ../html/login.html?error=email_exists");
        exit();
    }

    //Skapa en hash av lösenordet för en mer säker lagring i databasen
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    //Sparar användaren i databasen
    $insertSql = "INSERT INTO users (firstName, lastName, email, password) 
                  VALUES ('$firstName', '$lastName', '$email', '$hashed')";
    if ($conn->query($insertSql)) {
        header('Location: ../html/login.html?success=registered');
        exit();
    } else {
        header("Location: ../html/register.html?error=server_error");
        exit();
    }
}
