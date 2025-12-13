<?php
require_once 'start_session.php'; 
require_once 'db.php';

//Svar tillbaka sär i json format
header('Content-Type: application/json');

//Kontroll att det är ett formulär med post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    //Hämtar variabler från formuläret
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Om användaren vill lägga till i kundvagnen och id är giltigt
    if ($action === 'add' && $productId > 0) {
        
        // Kontrollera att sessionen är startad. Om sessionen inte fungerar, kommer detta vara tomt.
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        
        // Lägg till eller öka antalet
        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = 0;
        }
        $_SESSION['cart'][$productId] += $quantity;

        // Skicka tillbaka ett framgångsrikt svar
        echo json_encode(array('success' => true));
        exit;
    }
}

//Om något gick fel skicka felmeddelande
echo json_encode(array('success' => false, 'message' => 'Ogiltig åtgärd eller produkt-ID.'));

?>