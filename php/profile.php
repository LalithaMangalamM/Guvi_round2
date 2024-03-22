<?php
// profile.php
header("Access-Control-Allow-Origin: *");


// Include necessary files

require '../vendor/autoload.php';
use Predis\Client;
try {
    $redis = new Client();
    $redis->connect('127.0.0.1', 6379);
    //code...
} catch (Exception $e) {
    die($e -> getMessage());
    //throw $th;
}
$headers = getallheaders();
$sessionId = $headers['sessionid'];


// Retrieve session ID from the front end
echo "hi ".$sessionId;

// Retrieve user data from Redis using the session ID
$userData = $redis->get($sessionId); // Retrieve user data from Redis
print_r($userData);

// Check if user data exists
if ($userData) {
    // Parse user data from JSON
    $user = json_decode($userData, true);

    // Display user profile information
    echo 'Hi ' . $user['username'] . '<br>';
    echo 'Username: ' . $user['username'] . '<br>';
    echo 'Password: ' . $user['password'] . '<br>';
    echo 'Age: ' . $user['age'] . '<br>';
    echo 'DOB: ' . $user['dob'] . '<br>';
    echo 'Contact: ' . $user['contact'] . '<br>';
    echo 'Email: ' . $user['email'] . '<br>';

    // Add edit and logout buttons
    echo '<button id="editBtn">Edit</button>';
    echo '<button id="logoutBtn">Logout</button>';
} else {
    // If session ID is invalid or user data doesn't exist, redirect to login page
    // header('Location: login.html'); // Redirect to login page
    exit();
}

