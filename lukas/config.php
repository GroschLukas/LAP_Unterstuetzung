<?php
/**
 * opens a connection to the mysql db
 * if a exception will be thrown the message will be displayed
 */

$server = 'localhost';
$user = 'root';
$pwd ='';
$db = 'schueler1'; 
$conn = new mysqli($server, $user, $pwd, $db);


if ($conn->connect_error) {
    echo '<h3>Verbindung zur Datenbank fehlgeschlagen! Fehler: '. $conn->connect_error.'</h3>'; 
    die;
} 


