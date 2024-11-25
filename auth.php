<?php
session_start();

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $inputUsername = isset($_POST['username']) ? trim($_POST['username']) : '';
    $inputPassword = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Basic input validation
    if (empty($inputUsername) || empty($inputPassword)) {
        die(json_encode(['success' => false, 'message' => 'Username and password are required.']));
    }

    // Prepare the SQL statement to fetch the user
    $stmt = $conn->prepare("SELECT id, username, password, dispname FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $inputUsername); // Bind the username parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Fetch the user data
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($inputPassword, $user['password'])) {
		// Successful authentication
		$_SESSION['username'] = $user['username'];
		$_SESSION['dispname'] = $user['dispname'];

		echo json_encode(['success' => true, 'redirect' => 'main.php']);
            } else {
                // Authentication failed
                echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
            }
        } else {
            // User not found
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }

        $stmt->close();
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
