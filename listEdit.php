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
    if (!isset($_POST['itemid'])) die('no item specified!');
    $itemid = $_POST['itemid'];
    
    if (isset($_POST['edit'])) die('edit item ' . $itemid);
    else if (isset($_POST['delete'])) die('delete item '. $itemid);
    else if (isset($_POST['claim'])) die('claim item ' . $itemid);
    die('Bad trigger criteria!');
    $stmt = $conn->prepare('insert into list (userid, text, link, recurring) values (?, ?, ?, ?)');
    if ($stmt) {
        $stmt->bind_param('issi', $userid, $item, $link, $multiple);
        $stmt->execute();
        header('Location: main.php');
    } else die('insert failed');
} else die("no post");
?>


 
