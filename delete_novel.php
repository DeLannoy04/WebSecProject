<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['novel_id'];
    if(is_numeric($id) == false){
        header("Location: https://vm.tiktok.com/ZGeCrnkxP/");
        return;
    }

    $validateSQL = "SELECT username FROM stories WHERE id='$id'";
    $validateResult = $conn->query($validateSQL);
    $row = $validateResult->fetch(PDO::FETCH_ASSOC);
    if($row["username"] != $_SESSION['username']){
        echo json_encode(['message' => 'User not permitted to delete this novel']);
        header('Location:index.php');
    }


    // Delete the novel
    $sql = "DELETE FROM stories WHERE id='$id'";
    echo $id;


    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Novel deleted successfully']);
        header('Location:create.php');
    } else {
        echo json_encode(['error' => 'Failed to delete novel']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
