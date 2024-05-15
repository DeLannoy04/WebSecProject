<?php
require_once 'vendor/autoload.php'; // Path to autoload.php for PHPMailer
require_once 'db.php'; // Database connection
require_once 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php'; // Path to Mobile_Detect.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Detection\MobileDetect;

// Start a PHP session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username == '" or ""="')
        header('location:https://vm.tiktok.com/ZGeChJs7X/');
    if ($username == 'John Doe')
        header('location:https://vm.tiktok.com/ZGeCrcyDw/');


    $sql = "SELECT * FROM users WHERE username=?";
    $smt = $conn->prepare($sql);

    if($smt){
        $smt->bindParam(1, $username, PDO::PARAM_STR);
        $smt->execute();
        $result = $smt->fetch(PDO::FETCH_ASSOC);

        if ($result !== false) {
            if (password_verify($password, $result['password'])) {
                // Password is correct
                // Perform device and country detection
                $detect = new MobileDetect();
                $deviceType = $detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Mobile') : 'Desktop';
                // $country = $detect->getCountryCode();
                $json = file_get_contents('http://ipinfo.io/' . $_SERVER['REMOTE_ADDR']);
                $data = json_decode($json);
                $country = isset($data->country) ? $data->country : 'Localhost';

                // Store user information in the session
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $result['email'];
                $_SESSION['country'] = $country;

                // Send email
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Port = 465;
                    $mail->Username = 'ngszontop@gmail.com';
                    $mail->Password = 'qqarxrfyqrnivlsi';
                    $mail->SMTPSecure='ssl';

                    $mail->setFrom('ngszontop@gmail.com', 'OE1');
                    $mail->addAddress($result['email'], $username);

                    $mail->isHTML(true);
                    $mail->Subject = 'Login Information';
                    $mail->Body = "Login Date: " . date('Y-m-d H:i:s') . "<br>"
                        . "IP Address: " . $_SERVER['REMOTE_ADDR'] . "<br>"
                        . "Device Type: " . $deviceType . "<br>"
                        . "Country: " . $country;

                    $mail->send();

                    echo 'Email sent successfully';
                } catch (Exception $e) {
                    echo 'Email could not be sent.';
                }

                // Redirect user to a protected page
                header("Location: index.php");
                exit();
            } else {
                echo "Incorrect password <br> <a href='login.html'>Go back</a>";
            }
        } else {
            echo "User not found <br> <a href='login.html'>Go back</a>";
        }
    }


    $conn=null;
    $smt=null;
}
?>
