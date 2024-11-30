<?php
session_start();

// check that user owns item
function check_user_owns($uid, $itemid) {
    $stmt = $conn->prepare('select COUNT(id) from list where userid = ? and id = ?');
    $stmt->bind_param('ii',$userid,$itemid);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
    if ($count == 0) die("You are being bad. You don't want to mess with the database. Go home and rethink your life.");
    else if ($count != 1) die("Something impossible happened. Apparently there are multiple IDs that match this item.");
    return ($count == 1);
}

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
    else {
        $field = '';
        if (isset($_POST['delete'])) {
	    check_user_owns($userid, $itemid);
	    $stmt = $conn->prepare('update list set removed = 1 where id = ?');
	    $stmt->bind_param('i', $itemid);
        } else if (isset($_POST['claim'])) {
            $stmt = $conn->prepare('update list set claimed = ? where id = ?');
            $stmt->bind_param('ii', $userid, $itemid);
        } else if (isset($_POST['unclaim'])) {
            $stmt = $conn->prepare('update list set claimed = 0 where id = ?');
            $stmt->bind_param('i', $itemid);
        }
        else die('Invalid button');
        $stmt->execute();
        header('Location: main.php');
    }
} else die("no post");
?>


 
