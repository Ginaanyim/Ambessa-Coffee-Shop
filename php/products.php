<?php

require_once 'db.php'; 

//Mallfilen hämtas via absolut sökväg från webbrot
$productCardTemplate = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/product_item_template.html');

// Hämtar produkterna från databasen 
$sql = "SELECT id, name, description, price, image FROM products";
$result = $conn->query($sql);

// Skapar en tom sträng för att fylla med produkter
$productsHTML = '';

// kontrollerar att resultat från databsen finns och minst en produkt finns
if ($result && $result->num_rows > 0) {

    // Loopar igenom varje produkt som finns i databasen
    while ($row = $result->fetch_assoc()) {

        // Kopierar mallen för att fylla den med specifiks produkts data
        $productHTML = $productCardTemplate;

        // Ersätter placeholders med de riktiga värdena av produkterna från aktuella raden (row)
        $productHTML = str_replace('---ID---', $row['id'], $productHTML);
        $productHTML = str_replace('---NAME---', $row['name'], $productHTML);
        $productHTML = str_replace('---DESCRIPTION---', nl2br($row['description']), $productHTML);
        $productHTML = str_replace('---PRICE---', $row['price'], $productHTML);
        
        // Använd ENDAST filnamnet från databasen (kolumnen 'image')
        $productHTML = str_replace('---IMAGE---', $row['image'], $productHTML);        // Lägger till produkten i hela produktlistan
        $productsHTML .= $productHTML;
    }
} else {
    $productsHTML = 'No products to show';
}

// 4. KORRIGERAD SÖKVÄG: Huvudmallen hämtas via absolut sökväg från webbrot
$template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/products_template.html');

// Ersätter ---PRODUCTS--- med den färdiga html-strängen för alla produkter
$output = str_replace('---PRODUCTS---', $productsHTML, $template);

// skicka färdiga sidan till webbläsaren
echo $output;
?>