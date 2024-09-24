<?php
session_start();
require_once '../../config.php'; // Include database connection
require_once '../../model/user.php'; // Include User class

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $user = new User($conn);

    // Get form data
    $user->FullName = $_POST['full_name'];
    $user->Username = $_POST['username'];
    $user->Email = $_POST['email'];
    $user->Phone = $_POST['phone'];
    $user->PasswordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user->UserType = 'customer'; // Default user type
    $user->IsActive = true;

    // Check if email and username are unique
    if ($user->isUniqueEmail($user->Email)) {
        if ($user->isUniqueUsername($user->Username)) {
            // If both email and username are unique, add the user
            if ($user->add()) {
                echo "Registration successful! You can <a href='login.php'>login now</a>.";
            } else {
                echo "Registration failed!";
            }
        } else {
            echo "Username already exists!";
        }
    } else {
        echo "Email already exists!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <!-- Registration form -->
    <form method="POST" action="">
        <input type="text" name="full_name" placeholder="Full Name" required><br>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="phone" placeholder="Phone" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>