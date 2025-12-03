<?php
//Kopplingsinställningar till databasen
$host = 'localhost';     
$db = 'ambessa_store'; 
$user = 'root';       
$pass = '';          

//Ansluter till databasen
$conn = new mysqli($host, $user, $pass, $db);

//Kontroll av anslutningen
if ($conn->connect_error) {
    echo("Connection fail: " . $conn->connect_error);
}

