<?php
session_start();
require_once '../../config.php'; // Include database connection
require_once '../../model/user.php'; // Include User class

if (!isset($_SESSION['user_id'])) {
    echo "You need to <a href='login.php'>login</a> first!";
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn);

// Fetch the current user's data
$stmt = $user->fetch($_SESSION['user_id']);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Update account details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->UserID = $_SESSION['user_id'];
    $user->FullName = $_POST['full_name'];
    $user->Username = $_POST['username'];
    $user->Email = $_POST['email'];
    $user->Phone = $_POST['phone'];
    $user->LastLogin = date('Y-m-d H:i:s'); // Example update
    $user->IsActive = $user_data['IsActive'];
    $user->UserType = $user_data['UserType'];

    if ($user->update()) {
        echo "Account updated successfully!";
    } else {
        echo "Failed to update account!";
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
    <!-- Account details form -->
    <form method="POST" action="">
        <input type="text" name="full_name" value="<?= htmlspecialchars($user_data['FullName']) ?>" required><br>
        <input type="text" name="username" value="<?= htmlspecialchars($user_data['Username']) ?>" required><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user_data['Email']) ?>" required><br>
        <input type="text" name="phone" value="<?= htmlspecialchars($user_data['Phone']) ?>" required><br>
        <button type="submit">Update Account</button>
    </form>

    <a href="logout.php">Logout</a>
</body>
</html>


