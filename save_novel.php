<?php
session_start();
require_once "db.php";

$loggedIn = isset($_SESSION['username']);
$user = "";

if ($loggedIn) {
    // Get user details
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    // Get user's novels
    $novels = [];
    $sql = "SELECT * FROM stories WHERE username='$username'";
    $result = $conn->query($sql);
    $novelCount = 0;
    $firstNovel = '';
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $novels[] = $row;
            $novelCount++;
            $firstNovel = $row;
        }
    }
}
else{
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['titleInput'];
    $content = $_POST['contentInput'];
    $username = $_SESSION['username']; // Assuming the username is stored in the session

    $sql = "INSERT INTO stories (username, title, content) VALUES (:username, :title, :content)";
    $smt = $conn->prepare($sql);
    $smt->bindParam(':username', $username, PDO::PARAM_STR);
    $smt->bindParam(':title', $title, PDO::PARAM_STR);
    $smt->bindParam(':content', $content, PDO::PARAM_STR);
    $smt->execute();

    if ($smt->rowCount() > 0) {
        header("Location: create.php");
    } else {
        echo json_encode(['error' => 'Failed to save novel']);
    }
} else {
//    echo json_encode(['error' => 'Invalid request method']);
}

?>
