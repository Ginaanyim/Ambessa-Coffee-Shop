<?php
require_once 'start_session.php';
require_once 'db.php';

//Hanterar kundvagnsknapparna, öka, minska och ta bort produkt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    //Kontroll att produkt id är giltig och produkten finns i kundvagnen
    if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$productId]++; 
        } elseif ($action === 'decrease') {
            $_SESSION['cart'][$productId]--;
             //Tar bort produkten om antalet blir 0 eller mindre
            if ($_SESSION['cart'][$productId] <= 0) {
                unset($_SESSION['cart'][$productId]); 
            }
        } elseif ($action === 'remove') {
            unset($_SESSION['cart'][$productId]); 
        }
    }
    //Skicka användaren tillbaka till kundvagnssidan
    header("Location: cart.php");  
    exit();
}

//Ladda html mallen för kundvagnen
$page = file_get_contents('../html/cart_view.html');

//Hämtar produktmallen från html template
$productItemTemplate = '';
if (preg_match('/<template id="cart-item-template">(.*?)<\/template>/is', $page, $matches)) {
    $productItemTemplate = trim($matches[1]);
}
$cartItemsHtml = '';
$totalPrice = 0;

//Om kundvagnen är tom, meddelande
if (empty($_SESSION['cart'])) {
    $cartItemsHtml = "Your cart is empty."; 
    $totalText = "";
    $checkoutText = "";
} else {
    //Annars skapa en lista med produkt id:n för SQL frågan med kommatecken i mellan
    $productIds = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT id, name, price, image FROM products WHERE id IN ($productIds)";
    $result = $conn->query($sql);

    //Loopa genom varje produkt som finns i kundvagnen   
    if ($result && $result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            $id = $product['id'];
            $quantity = $_SESSION['cart'][$id];
            $subtotal = $product['price'] * $quantity;
            $totalPrice += $subtotal;

            //Skapar sökväg till produktbilden
            $imagePath = '../images/' . $product['image'];  

            //Byt ut placeholder i produktmallen med riktiga värden (med 2 decimaler)
            $itemHtml = str_replace(
                ['---NAME---', '---IMAGE---', '---QUANTITY---', '---PRICE---', '---SUBTOTAL---', '---PRODUCT_ID---'],
                [
                    htmlspecialchars($product['name']),
                    htmlspecialchars($imagePath),
                    $quantity,
                    number_format($product['price'], 2),
                    number_format($subtotal, 2),
                    $id
                ],
                $productItemTemplate
            );

            //Lägger till produkten i html för kundvagnen
            $cartItemsHtml .= $itemHtml;  
        }

        //Skriver ut totalpriset + checkout text
        $totalText = "Total: " . number_format($totalPrice, 2) . " SEK";  // Beräkna totalsumma
        $checkoutText = "Checkout"; 
    } else {
         //Felmeddelande om produkter inte kan hämtas
        $cartItemsHtml = "Failed to load products."; 
        $totalText = "";
        $checkoutText = "";
    }
}

//Ersätter placeholders i hela sidan med den html och totalsumman
$page = str_replace(
    ['---CART_ITEMS---', '---TOTAL---', '---CHECKOUT_BUTTON---'],
    [$cartItemsHtml, $totalText, $checkoutText],
    $page
);

//Visa sidan med kundvagnens innehåll
echo $page;  
