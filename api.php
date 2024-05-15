<?php
header('Content-Type: application/json');

require_once "db.php";

// Get API endpoint
$endpoint = $_SERVER['PATH_INFO'] ?? '/api';





// Handle different HTTP methods
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($endpoint === '/api') {
            $stmt = $conn->query('SELECT * FROM api');
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } else {
            $id = intval(explode('/', $endpoint)[1] ?? 0);
            $stmt = $conn->prepare('SELECT * FROM api WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($data);
        }
        break;
    case 'POST':
        if ($endpoint === '/api') {
            $data = json_decode(file_get_contents('php://input'), true);

            $stmt = $conn->prepare('INSERT INTO api (name, gender, birth_date) VALUES (:name, :gender, :birth_date)');
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':gender', $data['gender']);
            $stmt->bindParam(':birth_date', $data['birth_date']);
            $stmt->execute();
            echo json_encode(['message' => 'Entry created']);
        }
        else{
            echo json_encode(['message' => 'Incorrect request URL']);
        }
        break;
    case 'PATCH':
        if ($endpoint === '/api') {
            echo json_encode(['message' => 'You need to specify the id of the entry you want to update']);
            if(!is_numeric(explode('/', $endpoint)[1] ?? 0))
                echo json_encode(['message' => 'Invalid id format']);
        }
        else
        {
            $id = intval(explode('/', $endpoint)[1] ?? 0);
            $data = json_decode(file_get_contents('php://input'), true);
            $changedSmth = false;

            if(isset($data['name'])){
                $stmt = $conn->prepare('UPDATE api SET name = :name WHERE id = :id');
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':name', $data['name']);
                $stmt->execute();

                $changedSmth = true;

                echo json_encode(['message' => 'Name updated']);
            }

            if(isset($data['gender'])){
                if($changedSmth)
                    echo "\n";

                $stmt = $conn->prepare('UPDATE api SET gender = :gender WHERE id = :id');
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':gender', $data['gender']);
                $stmt->execute();

                $changedSmth = true;

                echo json_encode(['message' => 'Gender updated']);
            }

            if(isset($data['birth_date'])){
                if($changedSmth)
                    echo "\n";

                $stmt = $conn->prepare('UPDATE api SET birth_date = :birth_date WHERE id = :id');
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':birth_date', $data['birth_date']);
                $stmt->execute();
                echo json_encode(['message' => 'Birth date updated']);
            }

        }
        break;
    case 'DELETE':
        if($endpoint === '/api'){
            echo json_encode(['message' => 'Correct format: api.php/[id]']);
        }
        else {
            if(!is_numeric(explode('/', $endpoint)[1]))
                echo "Incorrect id format";
            else{
                $id = intval(explode('/', $endpoint)[1]);
                $stmt = $conn->prepare('DELETE FROM api WHERE id = :id');
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                echo json_encode(['message' => 'Entry removed']);
            }

        }
        break;
    default:
        echo json_encode(['error' => 'Method not allowed']);
}