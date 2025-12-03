<?php
require_once 'db.php';

//Hämtar produktens id från url:en och kontrollerar att det är ett heltal
$id = intval($_GET['id']);

//SQL-frågan för att hämta produkten med valt id
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

//Hämtar produktinformation som en array för att kunna använda $product['name] osv
$product = $result->fetch_assoc();

//Skapar sökväg till bilden
$imagePath = '/uppgifter/9/images/' . $product['image'];

//Läs in HTML mallen för produktsidan
$template = file_get_contents('../html/product-detail.html');

//Ersätter placeholders med data från databasen
$template = str_replace('---IMAGE---', $imagePath, $template);
$template = str_replace('---NAME---', $product['name'], $template);
$template = str_replace('---DESCRIPTION---', $product['description'], $template);
$template = str_replace('---PRICE---', $product['price'], $template);
$template = str_replace('---ID---', $product['id'], $template);

echo $template;
