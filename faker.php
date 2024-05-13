<?php
require_once 'vendor/autoload.php';
require_once 'db.php';

return; //We dont want users to be able to just call this

for($i = 0; $i < 50; $i++)
{
    $faker = Faker\Factory::create();

    $username = $faker->userName;
    $email = $faker->email;
    $password = password_hash($faker->password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $smt = $conn->prepare($sql);
    $smt->bindParam(':username', $username, PDO::PARAM_STR);
    $smt->bindParam(':email', $email, PDO::PARAM_STR);
    $smt->bindParam(':password', $password, PDO::PARAM_STR);
    $smt->execute();
    if ($smt->rowCount() > 0) {
        echo "Fake data Added <br>";
    }
    else{
        echo "Failed to Add fake data";
    }
}



