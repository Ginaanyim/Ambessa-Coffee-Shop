<?php
require_once 'start_session.php';
require_once 'db.php'; // Databasanslutning

//Skapa arrayer för fel och rätt meddelanden
$errors = [];
$success = '';

//Kontroll om formuläret har skickats med post + hämta fälten från formuläret
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $postalcode = isset($_POST['postalcode']) ? trim($_POST['postalcode']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $payment = isset($_POST['payment']) ? trim($_POST['payment']) : '';

    //Kontroller så obligatoriska fält ej är tomma
    if ($name === '') $errors[] = "Full Name is required.";
    if ($email === '') $errors[] = "Email is required.";
    if ($address === '') $errors[] = "Address is required.";
    if ($postalcode === '') $errors[] = "Postal Code is required.";
    if ($city === '') $errors[] = "City is required.";
    if ($payment === '') $errors[] = "Payment Method is required.";

    //Om inga fel och kundvagn ej är tom..
    if (empty($errors) && !empty($_SESSION['cart'])) {

        //..slå ihop adress till sträng för lättare att se i databasen. Hämta user_id om anävändare är inloggad annars NULL

        $fullAddress = $address . ", " . $postalcode . " " . $city;
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NULL";

          //Skapa en sträng med orderdetaljer som produktnamn och antal
        $orderDetails = "";
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $sql = "SELECT name FROM products WHERE id = $productId";
            $result = $conn->query($sql);
            if ($row = $result->fetch_assoc()) {
                $orderDetails .= $row['name'] . " (x" . $quantity . ")\n";
            }
        }

        //Sparar ordern i databasen
        $sql = "INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, customer_address, order_details, payment_method, order_date) 
            VALUES (
                $user_id,
                '$name',
                '$email',
                '$phone',
                '$fullAddress',
                '$orderDetails',
                '$payment',
                NOW()
            )";
        //Kontrollera om det lyckades + bekräftelse
        if ($conn->query($sql)) {
            $success = "Thank you, $name! Your order has been placed. Please make your payment with $payment to (123-45-67-89). Once the payment is received, we will send the product within 1-3 working days!";
            $_SESSION['cart'] = [];
        } else {
            $errors[] = "Error saving your order. Please try again.";
        }

    } elseif (empty($_SESSION['cart'])) {
        $errors[] = "Your cart is empty.";
    }
}

//Läs in html för checkout-sidan
$html = file_get_contents('../html/checkout.html');

//Visa meddelande som sucess eller error
$messages = !empty($success) ? $success : implode("\n", $errors);
$html = str_replace('---MESSAGES---', $messages, $html);

//Om ordern inte är lagd visa formuläret
$form = empty($success) ? file_get_contents('../html/checkout-form.html') : '';
$html = str_replace('---FORM---', $form, $html);

echo $html;
