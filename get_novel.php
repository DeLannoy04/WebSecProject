<?php
require_once "db.php";

$novelId = $_GET['id'];

if(!is_numeric($novelId)){
    header("Location: https://www.youtube.com/watch?v=vXNaw77-lUA");
    return;
}

$sql = "SELECT * FROM stories WHERE id='$novelId'";
$result = $conn->query($sql);
$data = $result->fetch(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
