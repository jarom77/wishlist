<?php
session_start(); // Start the session

// Check if the user is logged in (i.e., username session is set)
if (!isset($_SESSION['userid'])) {
    // If not logged in, redirect to the login page
    header("Location: login.html");
    exit;
}

// Retrieve and sanitize input
$inputPassword = isset($_POST['old_password']) ? trim($_POST['old_password']) : '';
if (strlen($inputPassword) < 10) die('Password is too short!');

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
    // Prepare the SQL statement to fetch the user
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $userid); // Bind the username parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Fetch the user data
            $user = $result->fetch_assoc();
            $stmt->close();

            // Verify the password
            if (password_verify($inputPassword, $user['password'])) {
                // Successful authentication
                $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
                $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
                if ($new_password == $confirm_password) {
                    $stmt = $conn->prepare("update users set password = ? where id = ?");
                    $stmt->bind_param('si', password_hash($new_password, PASSWORD_DEFAULT), $userid);
                    $stmt->execute();
                    $stmt->close();
                    echo json_encode(['success' => true, 'redirect' => 'main.php']);
                } else
                        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            } else {
                // Authentication failed
                echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
            }
        } else {
            // User not found
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare the statement.']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
