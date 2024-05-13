<?php
require_once 'vendor/autoload.php'; // Path to autoload.php for PHPMailer
require_once 'db.php'; // Database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username already exists
    $checkUsernameSql = "SELECT * FROM users WHERE username=:username";
    $smt = $conn->prepare($checkUsernameSql);
    $smt->bindParam(':username', $username, PDO::PARAM_STR);
    $smt->execute();
    $checkUsernameResult = $smt->fetchAll(PDO::FETCH_ASSOC);

    $checkEmailSql = "SELECT * FROM users WHERE email=:email";
    $smt = $conn->prepare($checkEmailSql);
    $smt->bindParam(':email', $email, PDO::PARAM_STR);
    $smt->execute();
    $checkEmailResult = $smt->fetchAll(PDO::FETCH_ASSOC);

    if (count($checkUsernameResult) > 0) {
        echo "Username already exists. Please choose a different username. <br> <a href='login.html'>Go Back</a>";
    }
    else if(count($checkEmailResult) > 0){
        echo "This email is used for an already existing account. Please use a different one. <br> <a href='login.html'>Go Back</a>";
    }
    else {
        // Username is available, proceed with registration
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $smt = $conn->prepare($sql);
        $smt->bindParam(':username', $username, PDO::PARAM_STR);
        $smt->bindParam(':email', $email, PDO::PARAM_STR);
        $smt->bindParam(':password', $password, PDO::PARAM_STR);
        $smt->execute();
        if ($smt->rowCount() > 0) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        }
        else {
            echo "Unexpected Error. <br> <a href='login.html'>Go Back</a>";
        }
    }
}
?>
