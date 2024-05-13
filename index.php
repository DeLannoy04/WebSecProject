<?php
session_start();
require_once "db.php";
$loggedIn = isset($_SESSION['username']);
$user = "";

if($loggedIn){
    // Get user details
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    $user = $result->fetch(PDO::FETCH_ASSOC);
}

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="home.css">
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
                <li><a href="?logout=true">Log Out</a></li>

                <?php else: ?>
                <li><a href="login.html">Log In</a></li>

                <?php endif; ?>
            </ul>
        </div>


    </div>
<!--    <div class="centerText">-->
<!--        <h1>Welcome to Our Website</h1>-->
<!--        --><?php //if ($loggedIn): ?>
<!--            <p>Hello, --><?php //echo $user['displayName']; ?><!--! You are logged in.</p>-->
<!--        --><?php //else: ?>
<!--            <p>You are not logged in. Please log in to access more features.</p>-->
<!--        --><?php //endif; ?>
<!--    </div>-->

    <div class="motto">
        <h1>Share your stories with the world.</h1>
        <p class="motto_quote">You can always edit a bad page. You can't edit a blank page. Create something.</p>
    </div>

    <div class="bottom-links">
        <?php if($loggedIn == false): ?>
        <a href="login.html " class="get-started-link">Get Started</a>
        <a href="browse_novels.php" class="browse-novel-link">Browse Novels</a>
        <?php else: ?>
            <a href="create.php" class="get-started-link">Write something</a>
            <a href="browse_novels.php" class="browse-novel-link">Browse Novels</a>
        <?php endif; ?>
    </div>


</body>
</html>
