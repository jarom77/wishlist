<!DOCTYPE html>
<?php
session_start(); // Start the session

// Check if the user is logged in (i.e., username session is set)
if (!isset($_SESSION['userid'])) {
    // If not logged in, redirect to the login page
    header("Location: login.html");
    exit;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h1>Change Password</h1>
        <form id="loginForm">
            <input type="password" name="old_password" id="old_password" placeholder="Old password" required>
            <input type="password" name="new_password" id="new_password" minlength="10" placeholder="New password" required>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
            <button type="submit">Update Password</button>
        </form>
        <p id="error-message" class="error-message">Incorrect password</p>
    </div>
    <script src="changePasswd.js"></script>
</body>
</html>

