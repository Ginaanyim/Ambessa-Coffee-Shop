<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Antar att db.php ligger i roten (../db.php)
require_once 'db.php';

//Hämtar produktens id från url:en och kontrollerar att det är ett heltal
$id = intval($_GET['id']);

//SQL-frågan för att hämta produkten med valt id
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

//Hämtar produktinformation som en array för att kunna använda $product['name] osv
$product = $result->fetch_assoc();

//Skapar KORREKT ABSOLUT sökväg till bilden från webbrot (/images/)
$imagePath = '/images/' . $product['image'];

//Läs in HTML mallen (Antar att ../product-detail.html pekar på filen i roten)
$template = file_get_contents('../product-detail.html');

//Ersätter placeholders med data från databasen
$template = str_replace('---IMAGE---', $imagePath, $template);
$template = str_replace('---NAME---', $product['name'], $template);
$template = str_replace('---DESCRIPTION---', $product['description'], $template);
$template = str_replace('---PRICE---', $product['price'], $template);
$template = str_replace('---ID---', $product['id'], $template);

echo $template;