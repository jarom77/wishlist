<?php
session_start();

if (!isset($_SESSION['userid'])) {
    // If not logged in, redirect to the login page
    header("Location: login.html");
    exit;
}
$userid = $_SESSION['userid'];

include 'config.php';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $item = isset($_POST['item']) ? trim($_POST['item']) : 'NULL';
    $link = isset($_POST['link']) ? trim($_POST['link']) : 'NULL';
    $multiple = isset($_POST['recurring']) ? 1 : 0;

    $stmt = $conn->prepare('insert into list (userid, text, link, recurring) values (?, ?, ?, ?)');
    if ($stmt) {
        $stmt->bind_param('issi', $userid, $item, $link, $multiple);
        $stmt->execute();
        header('Location: main.php');
    } else die('insert failed');
} else die("no post");
?>


 
