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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wish List</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="page-container">
        <h1>Family Gift List</h1>
        <div class="column-container" id="table-container">
            <div class="column" id="column-1">
                <h2><?php echo $dispname; ?></h2>
                <table>
                <?php
                    $stmt = $conn->prepare("SELECT text,link FROM list WHERE userid = ?");
                    if (!$stmt) die("failed to prepare statement");
                    $stmt->bind_param("s", $userid); // Bind the username parameter
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                        <td>' . $row['text'] . '</td>
                            <td>
                            <button onclick="location.href=\'script1.php\';">
                                <img class="icon" src="icon_edit.png" />
                            </button>
                            <button onclick="location.href=\'script2.php\';" class="trash">
                                <img class="icon" src="icon_delete.png">
                            </button>';
                        if ($row['link']) echo '
                            <a class="button" href="'.$row['link'].'"><img class="icon" src="icon_open.png"></a>';
                        echo '
                        </td>
                    </tr>';
                    }
                    $stmt->close();
                ?>
                </table>
                <div class="button-container">
                                    </div>
            </div>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>

