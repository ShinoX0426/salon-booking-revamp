<?php
session_start();
require_once '../../config.php'; // Include database connection
require_once '../../model/user.php'; // Include User class

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User object
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get login details from form submission
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user by username
    $stmt = $user->fetchByUsername($username);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists and the password matches
    if ($user_data && password_verify($password, $user_data['PasswordHash'])) {
        // Set session variables for logged-in user
        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['username'] = $user_data['Username'];
        $_SESSION['user_type'] = $user_data['UserType'];  // Store user type

        // Update LastLogin to the current timestamp
        $user->UserID = $user_data['user_id'];
        $user->LastLogin = date('Y-m-d H:i:s');  // Set the current date and time
        $user->updateLastLogin();  // Call function to update the last login

        // Redirect to admin or user dashboard based on user type
        if ($user_data['UserType'] === 'admin') {
            header('Location: ../admin/view_users.php');
        } else {
            header('Location: ../customer/dashboard.php');
        }
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- HTML login form -->
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>


