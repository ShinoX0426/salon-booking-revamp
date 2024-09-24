<?php
// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); // Redirect non-admin users
    exit();
}

// Include database connection and User class
require_once '../../config.php';
require_once '../../model/user.php';

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

// Check if user_id is passed via GET request
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Set the user ID in the user object
    $user->UserID = $user_id;

    // Delete the user
    if ($user->delete()) {
        // Redirect to the users list page with a success message
        header('Location: view_users.php?status=success&message=User deleted successfully.');
        exit();
    } else {
        // Redirect to the users list page with an error message
        header('Location: view_users.php?status=error&message=Failed to delete user.');
        exit();
    }
} else {
    // If no user ID is provided, redirect to users list
    header('Location: view_users.php');
    exit();
}
?>
