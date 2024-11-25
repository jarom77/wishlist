<?php
session_start(); // Start the session

// Check if the user is logged in (i.e., username session is set)
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username']; // Get the username from the session
$dispname = $_SESSION['dispname']; // Get the username from the session
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
                    <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td><a href="#">Sample Item 1</a></td>
                        <td>Sample Description 1</td>
			<td>
			    <button onclick="location.href='script1.php';">
                                <img class="icon" src="icon_edit.png" />
                            </button>
                            <button onclick="location.href='script2.php';" class="trash">
                                <img class="icon" src="icon_delete.png">
                            </button>
                        </td>
                    </tr>
                </table>
                <div class="button-container">
                                    </div>
            </div>
        </div>
    </div>
    <script src="main.js"></script>
</body>
</html>

