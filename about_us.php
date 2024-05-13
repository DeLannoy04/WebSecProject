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
    <title>About Us</title>
    <link rel="stylesheet" href="about_us.css">
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
            <li><a href="#">About Us</a></li>
            <?php if($loggedIn): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="?logout=true">Log Out</a></li>
            <?php else: ?>
                <li><a href="login.html">Log In</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="content-container">
    <div class="about-us-box">
        <h2>About Us</h2>
        <p>Welcome to our website! We are a team of three enthusiastic university students passionate about literature and technology. Our project focuses on creating a platform for novel enthusiasts to write, share, and explore stories in a user-friendly and interactive environment.</p>
        <h3>Our Mission</h3>
        <p>Our mission is to provide a creative space for aspiring writers to unleash their imagination and share their stories with the world. We aim to inspire and empower individuals to express themselves through writing and connect with like-minded individuals.</p>
        <h3>Meet the Team</h3>
        <ul>
            <li><strong>Bacsó Nándor</strong> - Backend Developer</li>
            <li><strong>Bán Gergő</strong> - Frontend Developer</li>
            <li><strong>Kaprinai Szabolcs</strong> - Quality Assurance Specialist</li>
        </ul>
        <h3>Join Us on Our Journey</h3>
        <p>We invite you to join us on this exciting journey of creativity and storytelling. Whether you're a seasoned writer or just starting, there's a place for you in our community. Together, let's bring stories to life and inspire the world with our words.</p>
        <p class="quote">"Every story has a beginning, a middle, and an end, but it is what we choose to do with them that defines us."</p>
    </div>
</div>
</body>
</html>
