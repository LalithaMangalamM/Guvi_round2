<?php
require '../vendor/autoload.php';

use MongoDB\Client;

// Connect to MongoDB
$mongoClient = new Client("mongodb://localhost:27017");

// Access a specific database
$database = $mongoClient->selectDatabase("userprofiles");

// Access a specific collection
$collection = $database->selectCollection("profiles");

$db_server = "localhost";
$db_username = "root";
$db_password = "";
$db_database = "registerdata";
$conn = "";


try {
    $conn = mysqli_connect($db_server, $db_username, $db_password, $db_database);
} catch (mysqli_sql_exception) {
    echo "not connected!!";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    $username = trim($_POST["username"]);
    if (empty($username)) {
        $errors["username"] = "Username required";
    }

    $email = trim($_POST["email"]);
    if (empty($email)) {
        $errors["email"] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format";
    }

    $password = trim($_POST["password"]);
    if (empty($password)) {
        $errors["password"] = "Password is required";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[0-9]).{6,}$/', $password)) {
        $errors["password"] = "Password must contain at least one uppercase letter and one number, and be at least 6 characters long";
    }
    else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
    }
    $dob = $_POST["dob"];
    $age = $_POST["age"];
    $contact = $_POST["contact"];


    if (empty($errors)) {
        $queryCriteria = ['email' => $email];
        $newDocument = [
            'username' => $username,
            'email' => $email,
            'password' => $hash,
        ];
        $existingDocument = $collection->findOne($queryCriteria);

        // If the document doesn't exist, insert it
        if (!$existingDocument) {
            $insertResult = $collection->insertOne($newDocument);
            if ($insertResult->getInsertedCount() > 0) {
                // echo "Document inserted successfully!";
                $insertedId = $insertResult->getInsertedId();
                $stmt = $conn->prepare("INSERT INTO register(id, dob, age, contact,username) VALUES (?,?,?,?,?)");
                $stmt->bind_param("ssiss", $insertedId, $dob, $age, $contact,$username);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    // Data was successfully inserted into the register table
                    $response = [
                        'success' => true,
                        'message' => 'Successfully registered',
                        'insertedId' => $insertedId
                    ];
                } else {
                    // Data insertion failed
                    $response = [
                        'success' => false,
                        'message' => 'Failed to insert data into register table'
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Failed to insert document into MongoDB'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'User already exists!'
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        header('Content-Type: application/json');
        echo json_encode($errors);
    }
}
