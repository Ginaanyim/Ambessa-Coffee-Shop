<?php
require_once 'start_session.php';
require_once 'db.php';

//Svar tillbaka är i json format
header('Content-Type: application/json');

//Skapar en tom lista för att fylla med produkter från kundvagnen
$items = [];

//Kollar om det finns något i kundvagnen
if (!empty($_SESSION['cart'])) {
    //Bygger en sträng med id:n, säkerställs att id:t är av heltal
    $idString = "";
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $idString .= intval($productId) . ",";
    }
    //Tar bort sista kommatecknet
    $idString = rtrim($idString, ",");

    //Hämtar produkterna från databasen
    $sql = "SELECT id, name, price, image FROM products WHERE id IN ($idString)";
    $result = $conn->query($sql);

    //Går igenom varje rad och lägger till i kundvagnens lista
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $quantity = $_SESSION['cart'][$id];

            $items[] = [
                'id'       => $id,
                'name'     => $row['name'],
                'price'    => $row['price'],
                'image'    => $row['image'],
                'quantity' => $quantity
            ];
        }
    }
}
//Skickar tillbaka kundvagnens innehåll som json
echo json_encode(['items' => $items]);
