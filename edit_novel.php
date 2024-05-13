<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $id = $data['novel_id'];
    $newTitle = $data['new_title'];
    $newContent = $data['new_content'];

    if(is_numeric($id) == false){
        header("Location: https://vm.tiktok.com/ZGeChLu9u/");
        return;
    }

    // Delete the novel
    $sql = "UPDATE stories SET title = ?, content=? WHERE id=? AND username=?";
//    $sql = "UPDATE stories SET title = '$newTitle', content='$newContent' WHERE id='$id' AND username='$_SESSION[username]'";

    $smt = $conn->prepare($sql);
    if($smt){
        $smt->bindParam(1, $newTitle, PDO::PARAM_STR);
        $smt->bindParam(2, $newContent, PDO::PARAM_STR);
        $smt->bindParam(3, $id, PDO::PARAM_INT);
        $smt->bindParam(4, $_SESSION['username'], PDO::PARAM_STR);
        $smt->execute();
    }

    $smt=null;
    $conn=null;

//    if ($conn->query($sql) === TRUE) {
//        echo json_encode(['message' => 'Novel deleted successfully']);
//        header('Location:create.php');
//    } else {
//        echo json_encode(['error' => 'Failed to delete novel']);
//    }
}
else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
