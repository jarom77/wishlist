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

$stmt = $conn->prepare("SELECT text,link FROM list WHERE userid = ?");
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
        echo "
                    <tr>
                        <td colspan=\"2\">${row['text']}</td>
                        <td class=\"buttons\" colspan=\"2\">";
        if ($row['link']) echo '
                            <a class="button" href="'.$row['link'].'"><img class="icon" src="icon_open.png"></a>';
        if ($person == NULL) echo '
                            <button onclick="location.href=\'script1.php\';" class="edit">
                                <img class="icon" src="icon_edit.png" />
                            </button>
                            <button onclick="location.href=\'script2.php\';" class="trash">
                                <img class="icon" src="icon_delete.png">
                            </button>';
        else echo '
                            <button onclick="location.href=\'script1.php\';" class="check">
                                <img class="icon" src="icon_check.png" />
                            </button>';
        echo '
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

