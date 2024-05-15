<?php
require 'vendor/autoload.php'; // Include PHPMailer autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);

session_start();
require_once "db.php";
$loggedIn = isset($_SESSION['username']);

if (isset($_GET['id'])) {
    $novelID = $_GET['id'];

    if(!is_numeric($novelID)){
        header("Location: browse_novels.php");
        return;
    }

    $sql = "SELECT * FROM stories WHERE id='$novelID'";
    $result = $conn->query($sql);
    $authorDisplayName = "Amongus";
    if ($result->rowCount() > 0) {
        $novel = $result->fetch(PDO::FETCH_ASSOC);
        $author = $novel['username'];
        $authorSql = "SELECT * FROM users WHERE username='$author'";
        $authorResult = $conn->query($authorSql);
        if ($authorResult->rowCount() > 0) {
            $authorDetails = $authorResult->fetch(PDO::FETCH_ASSOC);
            $pfpIndex = $authorDetails['pfpID'];
            $authorDisplayName = $authorDetails['displayName'];
            $emailNotification = $authorDetails['emailNotification'];
        }
    }
    else{
        header("Location: browse_novels.php");
    }
}
else
    header("Location: browse_novels.php");

// Handle comment submission
if (isset($_POST['submit'])) {
    echo "logged";
    if ($loggedIn) {
        echo "in";
        $commenterUsername = $_SESSION['username'];
        $content = $_POST['content'];
        $sql = "INSERT INTO comments (commenterUsername, content, novelID) VALUES ('$commenterUsername', '$content', '$novelID')";
        $conn->query($sql);
        header("Refresh:0"); // Refresh the page to show the new comment

        if ($emailNotification) {
            sendEmailNotification($authorDetails['email'], $novel['title'], $content);
        }
    } else {
        echo "Please log in to post a comment.";
    }
}

// Fetch comments for this novel
$comments = [];
$sql = "SELECT * FROM comments WHERE novelID='$novelID'";
$result = $conn->query($sql);
if ($result->rowCount() > 0) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $comments[] = $row;
    }

    foreach($comments as $index => $value){
        $displayNameSQL = "SELECT displayName FROM users WHERE userName = :commenterUsername";
        $stmt = $conn->prepare($displayNameSQL);
        if ($stmt) {
            $stmt->bindParam(':commenterUsername', $value['commenterUsername'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // Fetch the result
            $displayName = $result['displayName'];
            $comments[$index]['commenterUsername'] = $displayName;
        } else {
            echo "Error with communication with the database";
        }
    }
}
// Function to send email notification
function sendEmailNotification($recipientEmail, $novelTitle, $comment) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Port = 465;
        $mail->Username = 'ngszontop@gmail.com';
        $mail->Password = 'qqarxrfyqrnivlsi';
        $mail->SMTPSecure='ssl';

        $mail->setFrom('ngszontop@gmail.com', 'OE1');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);                                      // Set email format to HTML
        $mail->Subject = 'New Comment Notification';
        $mail->Body    = 'Your novel <strong>' . $novelTitle . '</strong> has a new comment. <br> <br>' . $comment;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $novel['title']; ?></title>
    <link rel="stylesheet" href="novel_details.css">
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
    <h2><?php echo $novel['title']; ?></h2>
    <div class="novel-details">
        <div class="ppimg"><img src="profile_pictures/<?php echo $pfpIndex; ?>.jpg" alt="Profile Picture"></div>
        <div class="author-info">
            <p><strong><?php echo $authorDisplayName; ?></strong> </p>

        </div>

        <div class="author-content">
<!--            <p><strong>Content:</strong></p>-->
            <p><?php echo $novel['content']; ?></p>
        </div>
        <div class="author-publishtime">
            <p><strong>Published:</strong> <?php echo $novel['creationDate']; ?></p>
        </div>


    </div>



    <div class="comments">
        <h3>Comments</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p><strong><?php echo $comment['commenterUsername']; ?></strong> <?php echo $comment['content']; ?></p>
                <div class="commentDate">
                    <p><?php echo $comment['creationDate']; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($loggedIn): ?>
        <div class="comment-form">
            <h3>Post a Comment</h3>
            <form method="post">
                <div class="commentcontent">
                    <textarea name="content" placeholder="Write your comment here"></textarea>
                </div>
                <button class="novelDetailsButton" type="submit" name="submit">Submit</button>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
