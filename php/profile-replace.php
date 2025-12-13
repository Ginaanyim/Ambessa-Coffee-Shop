<?php

require_once  'start_session.php';
require_once  'db.php'; 

// Kontroll om användaren är inloggad annars vidare till login sida
if (!isset($_SESSION['user_id'])) {
    // KORRIGERAD LÄNK: Använder absolut sökväg för HTTP-omdirigering (bäst praxis)
    header("Location: /login.html");
    exit();
}

// Hämtar användarens ID från sessionen
$userId = $_SESSION['user_id'];

// Hämta användarens information från databasen
$sqlUser = "SELECT firstName, lastName, email FROM users WHERE id = $userId";
$result = $conn->query($sqlUser);

// Kollar om anvndaren finns i databsen, hämta namn, email, annars stopp med felmeddelande
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullName = $user['firstName'] . ' ' . $user['lastName'];
    $email = $user['email'];
} else {
    // Användaren finns inte, förstör sessionen och omdirigera till login
    session_destroy();
    header("Location: /login.html");
    exit();
}

// Hämta användarens eventuella orders från databasen
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
// KORRIGERAD SÖKVÄG: Använder __DIR__ för att garantera att PHP hittar mallen i föräldramappen
$profileHtmlPath = __DIR__ . '/../profile.html';
$html = file_get_contents($profileHtmlPath);

// Lägg till en kontroll om filen inte kunde laddas
if ($html === FALSE) {
    die("Error: Kunde inte ladda profilmallen. Kontrollera sökvägen till profile.html: " . htmlspecialchars($profileHtmlPath));
}

// Ersätt platshållare med användardata och orders
$html = str_replace('---username---', $fullName, $html);
$html = str_replace('---user-email---', $email, $html);
$html = str_replace('---user-orders---', nl2br(htmlspecialchars($ordersText)), $html);

// Visa sidan
echo $html;
?>