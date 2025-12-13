<?php
//Kopplingsinställningar till databasen
$host = 'database-5019171788.webspace-host.com';     
$db = 'dbs15055826'; 
$user = 'dbu1302384';       
$pass = 'Yirgalem12';          

//Ansluter till databasen
$conn = new mysqli($host, $user, $pass, $db);

//Kontroll av anslutningen
if ($conn->connect_error) {
    echo("Connection fail: " . $conn->connect_error);
}

