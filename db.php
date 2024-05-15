<?php
$servername = "localhost";
$username = "oe1";
$password = "WklUmZFB5msQQU5";
$dbname = "oe1";

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);



try {
    $conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);}
catch (PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}
//
//// Check connection
//if ($conn->connect_error) {
//    die("Connection failed: ");
//}
?>
