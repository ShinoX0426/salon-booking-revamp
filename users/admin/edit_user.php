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

    // Fetch the user details
    $stmt = $user->fetch($user_id);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and update user data
        $user->UserID = $user_id;
        $user->FullName = $_POST['full_name'];
        $user->Username = $_POST['username'];
        $user->Email = $_POST['email'];
        $user->Phone = $_POST['phone'];
        $user->UserType = $_POST['user_type'];
        $user->IsActive = isset($_POST['is_active']) ? 1 : 0;

        // Update the user in the database
        if ($user->update()) {
            // Redirect to the users list with a success message
            header('Location: view_users.php?status=success&message=User updated successfully.');
            exit();
        } else {
            echo "Failed to update user.";
        }
    }
} else {
    // Redirect to users list if no user ID is provided
    header('Location: view_all_users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>

    <form action="edit_user.php?user_id=<?= htmlspecialchars($user_id) ?>" method="POST">
        <label for="full_name">Full Name:</label><br>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user_data['FullName']) ?>" required><br><br>

        <label for="username">Username:</label><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user_data['Username']) ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user_data['Email']) ?>" required><br><br>

        <label for="phone">Phone:</label><br>
        <input type="text" name="phone" value="<?= htmlspecialchars($user_data['Phone']) ?>" required><br><br>

        <label for="user_type">User Type:</label><br>
        <select name="user_type" required>
            <option value="admin" <?= ($user_data['UserType'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="customer" <?= ($user_data['UserType'] === 'customer') ? 'selected' : '' ?>>User</option>
            <option value="staff" <?= ($user_data['UserType'] === 'staff') ? 'selected' : '' ?>>Staff</option>
            <option value="stylist" <?= ($user_data['UserType'] === 'stylist') ? 'selected' : '' ?>>Stylist</option>
        </select><br><br>

        <label for="is_active">Is Active:</label><br>
        <input type="checkbox" name="is_active" <?= ($user_data['IsActive'] ? 'checked' : '') ?>><br><br>

        <input type="submit" value="Update User">
    </form>

    <a href="view_users.php">Back to User List</a>
</body>
</html>
