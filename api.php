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
            $id = intval(explode('/', $endpoint)[2] ?? 0);
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
            $stmt = $conn->prepare('INSERT INTO "API" (name, gender, birth_date) VALUES (:name, :gender, :birth_date)');
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':gender', $data['gender']);
            $stmt->bindParam(':birth_date', $data['birth_date']);
            $stmt->execute();
            echo json_encode(['message' => 'API created']);
        }
        break;
    case 'PATCH':
        if ($endpoint === '/api') {
            $id = intval(explode('/', $endpoint)[2] ?? 0);
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare('UPDATE "API" SET name = :name, gender = :gender, birth_date = :birth_date WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':gender', $data['gender']);
            $stmt->bindParam(':birth_date', $data['birth_date']);
            $stmt->execute();
            echo json_encode(['message' => 'API updated']);
        }
        break;
    case 'DELETE':
        if ($endpoint === '/api') {
            $id = intval(explode('/', $endpoint)[2] ?? 0);
            $stmt = $conn->prepare('DELETE FROM "API" WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            echo json_encode(['message' => 'API deleted']);
        }
        break;
    default:
        echo json_encode(['error' => 'Method not allowed']);
}