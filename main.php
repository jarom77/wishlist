<?php
session_start(); // Start the session

// Check if the user is logged in (i.e., username session is set)
if (!isset($_SESSION['userid'])) {
    // If not logged in, redirect to the login page
    header("Location: login.html");
    exit;
}

$userid = $_SESSION['userid']; // Get the username from the session
$dispname = $_SESSION['dispname']; // Get the username from the session

include 'config.php';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wish List</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="page-container">
        <div class="header-container">
            <h1>Family Gift List</h1>
            <button class="global-button" id="passwd" onclick="location.href='changePasswd.php'">Change Password</button>
        </div>
        <div class="column-container" id="table-container">
<?php
$access_stmt = $conn->prepare("SELECT can_view, dispname FROM access inner join users on access.can_view = users.id WHERE uid = ? order by users.id");
if (!$access_stmt) die("failed to prepare statement");
$access_stmt->bind_param("s", $userid); // Bind the username parameter
$access_stmt->execute();
$access_result = $access_stmt->get_result();

$stmt = $conn->prepare("SELECT id,text,link,recurring,claimed FROM list WHERE userid = ?");
if (!$stmt) die("failed to prepare statement");
$person = NULL;
do {
    echo '
            <div class="column"';
    if ($person == NULL) echo 'id="column-1"';
    echo '>
                <h2>';
    if ($person == NULL) echo $dispname;
    else echo $person['dispname'];
    echo '</h2>
                <table>';
    if ($person == NULL) $stmt->bind_param("d", $userid); // Bind the username parameter
    else $stmt->bind_param("d", $person['can_view']);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $notes = '';
        $userClaimed = false;
        $userItem = NULL;
        if ($person != NULL && $row['recurring']) {
            $getNotes = $conn->prepare('select note from notes where itemid = ?;');
            $getNotes->bind_param("i", $row['id']);
            $getNotes->execute();
            $allNotes = $getNotes->get_result();
            while ($next_note = $allNotes->fetch_assoc()) $notes = $notes . $next_note['note'] . ', ';
            if (strlen($notes) > 1) $notes = substr($notes, 0, -2);
            if (!$notes) $notes = 'none';
            $getNotes->close();

            $getNotes = $conn->prepare('select note from notes where itemid = ? && userid = ?;');
            $getNotes->bind_param('ii', $row['id'], $userid);
            $getNotes->execute();
            $rows = $getNotes->get_result()->fetch_row();
            $userClaimed = $rows !== NULL;
            if ($userClaimed) $userItem = $rows[0];
            if ($userItem) $userItem = " <b>(you: " . $userItem . ')</b>';
            $getNotes->close();
        }
        $claimed_button_code = 'type="button" onclick="openDatePicker(' . $row['id'] . ',\'' . $notes . '\')"';
        if ($row['claimed'] == $userid || $userClaimed) {
            $claimed_button_code = 'type="submit" style="background-color: red"';
        }
        $row_classes = '';
        if ($person != NULL && ($userClaimed || $row['claimed'] && !$row['recurring'])) $row_classes .= 'claimed ';
        if ($person == NULL && $row['recurring']) $row_classes .= 'recurring ';
        echo "
                    <tr class=\"$row_classes\" id=\"item${row['id']}\">
                        <td>${row['text']}$userItem</td>
                        <td class=\"buttons\">";
        if ($row['link']) echo '
                            <a class="button" href="'.$row['link'].'"><img class="icon" src="icon_open.png"></a>';
        echo '
                            <form action="listEdit.php" method="post">
                                <input hidden name="itemid" value="' . $row['id'] . '">';
        if ($person == NULL) echo '
                                <button type="button" onclick="openItemWindow(' . $row['id'] .', '. $row['recurring'] .')" name="edit" class="edit">
                                    <img class="icon" src="icon_edit.png" />
                                </button>
                                <button type="submit" name="delete" class="trash">
                                    <img class="icon" src="icon_delete.png">
                                </button>';
        else if (!$row['claimed'] || $row['claimed'] == $userid || $row['recurring']) echo '
                                <button '.$claimed_button_code.' name="unclaim" class="check">
                                    <img class="icon" src="icon_check.png" />
                                </button>';
        echo '
                            </form>
                        </td>
                    </tr>';
    }
    echo '
                </table>';
    if ($person == NULL) echo '
                <p>Green highlight denotes multiple items</p>
                <button class="global-button check" onclick="openItemWindow(0,0)">Add New</button>';
    echo '
            </div>';
} while ($person = $access_result->fetch_assoc());

$stmt->close();
$access_stmt->close();
?>
        </div>
    </div>
    <div id="datePickerModal" class="modal">
        <div class="modal-content">
            <form action="listEdit.php" method="post">
                <h2>Select date gift will be opened</h2>
                <input required type="date" id="selectedDate" name="giftDate">
                <div id="multiple-notes">
                    <p>Already taken:</p>
                    <p id="notes-text"></p>
                    <input name="note" type="text" maxlength="20" placeholder="color, type, etc">
                </div>
                <input hidden name="itemid" id="dateItemId" value="0">
                <div class="button-container">
                    <button class="dateSubmit" name="claim">Claim</button>
                </div>
            </form>
        </div>
    </div>
    <div id="itemModal" class="modal">
        <div class="modal-content">
            <form action="addItem.php" method="post">
                <h2>Enter item information</h2>
                <input name="item" id="desc" type="text" maxlength="255" placeholder="description" required>
                <textarea name="link" id="link" type="text" maxlength="400" placeholder="link [optional]"></textarea>
                <input type="checkbox" name="recurring" id="recurring">
                <label for="recurring">Multiple</label>
                <input hidden name="itemid" id="editItemId" value="0">
                <div class="button-container">
                    <button class="dateSubmit" name="submit">Done</button>
                </div>
            </form>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>

