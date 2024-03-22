<?php
require '../vendor/autoload.php';

use Predis\Client;
use MongoDB\Client as MongoDBClient;

$redis = new Client();

$mongoClient = new MongoDBClient("mongodb://localhost:27017");

$database = $mongoClient->selectDatabase("userprofiles");

$collection = $database->selectCollection("profiles");

$mysqli = new mysqli("localhost", "root", "", "registerdata");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$email = $_POST["email"];
$password = $_POST["password"];

$user = $collection->findOne(["email" => $email]);

if ($user && password_verify($password, $user["password"])) {
    $filter = ['email' => $email];
    $mongoResult = $collection->findOne($filter);

    $stmt = $mysqli->prepare("SELECT * FROM register WHERE username = ?");

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    $mongoArray = iterator_to_array($mongoResult);

    $mysqlArray = $result->fetch_all(MYSQLI_ASSOC);
    $combinedArray = array_merge($mongoArray, $mysqlArray);
    $values = serialize($combinedArray);

    $redis->connect('127.0.0.1', 6379);
    $sessionId = uniqid();
    // print_r($values);
    $redis->set($sessionId, $values);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'sessionId' => $sessionId]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}
