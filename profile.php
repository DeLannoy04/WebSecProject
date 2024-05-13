<?php
session_start();
require_once 'db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Get user details
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);
$user = $result->fetch(PDO::FETCH_ASSOC);

// Profile picture selection logic
if (isset($_POST['SaveProfilePicture'])) {
    $pfpID = $_POST['pfpID'];
    if(!is_numeric($pfpID)){
        header("Location: https://www.youtube.com/shorts/4GIyrFB_Rgw");
        return;
    }
    $updateSql = "UPDATE users SET pfpID='$pfpID' WHERE username='$username'";
    $conn->query($updateSql);
    header("Location: profile.php");
    exit();
}
// Display name update
if (isset($_POST['saveDisplayName'])) {
    $displayName = $_POST['displayName'];
    $updateSql = "UPDATE users SET displayName=? WHERE username='$username'";
    $smt = $conn->prepare($updateSql);
    if($smt){
        $smt->bindParam(1, $displayName, PDO::PARAM_STR);
        $smt->execute();
        $result = $smt->fetchAll(PDO::FETCH_ASSOC);
        header("Location: profile.php");
    }



}
// Bio update
if (isset($_POST['saveBio'])) {
    $bio = $_POST['bio'];
    $updateSql = "UPDATE users SET bio=:bio WHERE username=:username";
    $smt = $conn->prepare($updateSql);
    if($smt){
        $smt->bindParam(':bio', $bio, PDO::PARAM_STR);
        $smt->bindParam(':username', $username, PDO::PARAM_STR);
        $smt->execute();
        header("Location: profile.php");
    }
}


// Email Notification update
if (isset($_POST['saveEmailNotification'])) {
    $emailNotification = isset($_POST['emailNotification']) ? 1 : 0; // Convert checkbox value to 1 or 0
    $updateSql = "UPDATE users SET emailNotification='$emailNotification' WHERE username='$username'";
    $conn->query($updateSql);
    header("Location: profile.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

</head>
<body>
<div class="navbar">

    <div class="navbar-left">
        <a href="index.php" class="logo-container">
            <a href="index.php"><img src="profile_pictures/logo.png" alt="" style="width: 60px; height: 60px"></a>

        </a>

    </div>
    <div class="site-name">Writer's Nest</div>

    <div class="navbar-right">
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="index.php?logout=true">Log Out</a></li>
        </ul>
    </div>


</div>

<div class="overlay"></div>
<div class="container">
    <h1 class="centerText"><?php echo $user['displayName']; ?></h1>
    <!--    <h2>Profile Picture</h2>-->
    <div class="profile-picture">
        <img src="profile_pictures/<?php echo $user['pfpID']; ?>.jpg" alt="Profile Picture" onclick="openPopup()">
    </div>
    <!--    <h2>Change Profile Picture</h2>-->
    <div id="popup" class="popup">
        <div class="popup-content">
            <?php for ($i = 0; $i < 10; $i++): ?>
                <img src="profile_pictures/<?php echo $i; ?>.jpg" alt="Profile Picture <?php echo $i; ?>" class="<?php echo ($user['pfpID'] == $i) ? 'selected' : ''; ?>" onclick="selectProfilePicture(<?php echo $i; ?>)">
            <?php endfor; ?>
            <form method="post">
                <input type="hidden" id="selectedPfpID" name="pfpID" value="<?php echo $user['pfpID']; ?>">
                <input type="submit" name="SaveProfilePicture" value="Save">
                <button type="button" onclick="closePopup()">Cancel</button>
            </form>
        </div>
    </div>
    <h2>Change Display Name</h2>
    <form method="post">
        <input type="text" name="displayName" value="<?php echo $user['displayName']; ?>">
        <input type="submit" name="saveDisplayName" value="Save">
    </form>
    <h2>Email</h2>
    <p class="emailValue"><?php echo $user['email']; ?></p>
    <h2>Email Notifications</h2>
    <form method="post">
        <input type="checkbox" name="emailNotification" <?php echo ($user['emailNotification'] == 1) ? 'checked' : ''; ?>> Receive email notifications
        <span class="tooltip">
        <i class="fa fa-question-circle"></i>
        <span class="tooltip-text">Checking this box will make that every time someone comments or rates your novel, we will send an e-mail about it</span>
    </span>
        <input type="submit" name="saveEmailNotification" value="Save">
    </form>

    <h2>Bio</h2>
    <form method="post">
        <textarea name="bio" placeholder="About me"><?php echo $user['bio']; ?></textarea>
        <input type="submit" name="saveBio" value="Save">
    </form>
</div>

<script>
    function openPopup() {
        document.getElementById("popup").style.display = "block";
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
    }

    function selectProfilePicture(pfpID) {
        document.getElementById("selectedPfpID").value = pfpID;
        var images = document.getElementsByClassName("selected");
        for (var i = 0; i < images.length; i++) {
            images[i].classList.remove("selected");
        }
        document.querySelector('img[src="profile_pictures/' + pfpID + '.jpg"]').classList.add("selected");
    }
</script>
</body>
</html>
