<?php
session_start();
require_once "db.php";

$loggedIn = isset($_SESSION['username']);

$novels = [];
$sql = "SELECT * FROM stories";
$result = $conn->query($sql);
if ($result->rowCount() > 0) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $novels[] = $row;
    }
}

foreach($novels as $index => $value){
    $displayNameSQL = "SELECT displayName FROM users WHERE userName = :username";
    $stmt = $conn->prepare($displayNameSQL);
    if ($stmt) {
        $stmt->bindParam(':username', $value['username'], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $displayName = $result['displayName'];
            $novels[$index]['username'] = $displayName;
        }
    }
    else {
        echo "Error with communication with the database";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Novels</title>
    <link rel="stylesheet" href="browse_novels.css">
</head>
<body>
<div class="overlay"></div>
<div class="navbar">
    <div class="navbar-left">
        <a href="index.php" class="logo-container">
            <a href="index.php"><img src="profile_pictures/logo.png" alt="" style="width: 60px; height: 60px"></a>
        </a>
    </div>
    <div class="site-name">Writer's Nest</div>
    <div class="navbar-right">
        <ul class="nav-links">
            <li><a href="about_us.php">About Us</a></li>

            <?php if($loggedIn): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="index.php?logout=true">Log Out</a></li>

            <?php else: ?>
                <li><a href="login.html">Log In</a></li>

            <?php endif; ?>
        </ul>
    </div>
</div>
<div class="container">
    <h2>Browse Novels</h2>
    <div class="novel-list">
        <?php foreach ($novels as $novel): ?>
            <a href="novel_details.php?id=<?php echo $novel['id']; ?>" class="novel-box">
                <h3><?php echo $novel['title']; ?></h3>
                <p>By: <?php echo $novel['username']; ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
