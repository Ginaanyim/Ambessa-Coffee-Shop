<?php
require_once 'start_session.php';
require_once 'db.php'; 

//Kontroll om användaren är inloggad annars vidare till login sida
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.html");
    exit();
}

//Hämtar användarens ID från sessionen
$userId = $_SESSION['user_id'];

//Hämta användarens information från databasen
$sqlUser = "SELECT firstName, lastName, email FROM users WHERE id = $userId";
$result = $conn->query($sqlUser);

//Kollar om anvndaren finns i databsen, hämta namn, email, annars stopp med felmeddelande
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullName = $user['firstName'] . ' ' . $user['lastName'];
    $email = $user['email'];
} else {
    die("User not found.");
}

//Hämta användarens eventuella orders från databasen
$sqlOrders = "SELECT order_details, order_date FROM orders WHERE customer_email = '$email' ORDER BY order_date DESC";
$result = $conn->query($sqlOrders);

$ordersText = "";
if ($result && $result->num_rows > 0) {
    while ($order = $result->fetch_assoc()) {
        $ordersText .= "Date: " . $order['order_date'] . "\n";
        $ordersText .= $order['order_details'] . "\n\n";
    }
} else {
    $ordersText = "You have no orders yet.";
}

// Läs HTML-mallen för profilen
$html = file_get_contents('../html/profile.html');

// Ersätt platshållare med användardata och orders
$html = str_replace('---username---', $fullName, $html);
$html = str_replace('---user-email---', $email, $html);
$html = str_replace('---user-orders---', nl2br(htmlspecialchars($ordersText)), $html);

// Visa sidan
echo $html;

