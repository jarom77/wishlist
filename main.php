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
        <h1>Family Gift List</h1>
        <div class="column-container" id="table-container">
<?php
$access_stmt = $conn->prepare("SELECT can_view, dispname FROM access inner join users on access.can_view = users.id WHERE uid = ?");
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
        $claimed_button_code = '';
	$claimName = 'claim';
	if ($row['claimed'] == $userid) {
            $claimed_button_code = ' style="background-color: red"';
            $claimName = 'unclaim';
        }
        $claimed_style = '';
        if ($row['claimed'] && $person != NULL) $claimed_style = ' style="background-color: rgba(255,0,0,0.25)"';

        echo "
                    <tr$claimed_style>
                        <td colspan=\"2\">${row['text']}</td>
                        <td class=\"buttons\" colspan=\"2\">";
        if ($row['link']) echo '
                            <a class="button" href="'.$row['link'].'"><img class="icon" src="icon_open.png"></a>';
        echo '
                            <form action="listEdit.php" method="post">
                                <input hidden name="itemid" value="' . $row['id'] . '">';
        if ($person == NULL) echo '
                                <button type="submit" name="edit" class="edit">
                                    <img class="icon" src="icon_edit.png" />
                                </button>
                                <button type="submit" name="delete" class="trash">
                                    <img class="icon" src="icon_delete.png">
                                </button>';
        else if (!($row['recurring'] || $row['claimed'] != 0 && $row['claimed'] != $userid)) echo '
                                <button type="submit" name="'. $claimName .'" class="check"' . $claimed_button_code . '>
                                    <img class="icon" src="icon_check.png" />
                                </button>';
        echo '
                            </form>
                        </td>
                    </tr>';
    }
    if ($person == NULL) echo '
                    <tr>
                        <form action="addItem.php" method="POST">
                            <td class="item"><input name="item" required type="text" maxlength="255" placeholder="description"></td>
                            <td class="link"><input name="link" type="text" maxlength="400" placeholder="link [optional]"></td>
                            <td style="text-align: center;">Multiple<input type="checkbox" name="recurring" value="true"></td>
                            <td class="buttons"><button type="submit" class="check">
                                <img class="icon" src="icon_plus.png" />
                            </button></td>
                        </form>
                    </tr>';
    echo '
                </table>
            </div>';
} while ($person = $access_result->fetch_assoc());

$stmt->close();
$access_stmt->close();
?>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>

