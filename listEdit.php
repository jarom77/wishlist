<?php
session_start();

// check that user owns item
function user_owns($conn, $uid, $itemid) {
    $stmt = $conn->prepare('select COUNT(id) from list where userid = ? and id = ?');
    $stmt->bind_param('ii', $uid, $itemid);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
    if ($count > 1) die("Something impossible happened. Apparently there are multiple IDs that match this item.");
    return ($count == 1);
}

function check_user_owns($conn, $uid, $itemid, $shouldOwn) {
    if ($shouldOwn ^ user_owns($conn, $uid, $itemid))
        die("You are being bad. You don't want to mess with the database. Go home and rethink your life.");
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
            check_user_owns($conn, $userid, $itemid, True);
            $stmt = $conn->prepare('delete from list where id = ?');
            $stmt->bind_param('i', $itemid);
        } else if (isset($_POST['claim'])) {
            check_user_owns($conn, $userid, $itemid, False);
            $giftDate = $_POST['giftDate'];
            $stmt = $conn->prepare('update list set claimed = ?, giftDate = ? where id = ? && recurring = 0');
            $stmt->bind_param('isi', $userid, $giftDate, $itemid);

            # determine if item is recurring
            $recurCheck = $conn->prepare('select recurring from list where id = ?');
            $recurCheck->bind_param('i', $itemid);
            $recurCheck->execute();
            $isRecurring = $recurCheck->get_result()->fetch_row()[0];
            $recurCheck->close();

            # if recurring, insert 'note' field value
            if ($isRecurring) {
                $stmt->execute();
                $stmt->close();
                // add note
                $note = $_POST['note'];
                $stmt = $conn->prepare('insert into notes(itemid, userid, note) values (?, ?, ?)');
                $stmt->bind_param('iis', $itemid, $userid, $note);
            }
        } else if (isset($_POST['unclaim'])) {
            $stmt = $conn->prepare('update list set claimed = 0, giftDate = NULL where id = ?');
            $stmt->bind_param('i', $itemid);
            $stmt->execute();
            $stmt->close();
        
            // remove notes
            $stmt = $conn->prepare('delete from notes where itemid = ? && userid = ?');
            $stmt->bind_param('ii', $itemid, $userid);
        }
        else die('Invalid button');
        $stmt->execute();
        header('Location: main.php');
    }
} else die("no post");
?>


 
