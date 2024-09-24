<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: ../account_control/login.php');
    exit();
}

// Check if the logged-in user is an admin
if ($_SESSION['user_type'] !== 'admin') {
    // Redirect to the homepage or show an error message if not an admin
    header('Location: ../index.php');
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

// Initialize search, sort, and filter variables
$search = '';
$orderBy = 'user_id'; // Default sorting
$orderDir = 'ASC'; // Default sorting direction
$userTypeFilter = 'all'; // Default filter

// Handle form submission for search, sort, and filter
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }
    if (isset($_GET['order_by'])) {
        $orderBy = $_GET['order_by'];
    }
    if (isset($_GET['order_dir'])) {
        $orderDir = $_GET['order_dir'];
    }
    if (isset($_GET['user_type_filter'])) {
        $userTypeFilter = $_GET['user_type_filter'];
    }
}

// Fetch all users with filtering and sorting
$stmt = $user->fetchAllUsers($search, $orderBy, $orderDir, $userTypeFilter);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View All Users</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>All Users</h1>

    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by username or email" value="<?= htmlspecialchars($search) ?>">
        
        <select name="user_type_filter">
            <option value="all">All User Types</option>
            <option value="admin" <?= $userTypeFilter == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="customer" <?= $userTypeFilter == 'customer' ? 'selected' : '' ?>>Customer</option>
            <option value="stylist" <?= $userTypeFilter == 'stylist' ? 'selected' : '' ?>>Stylist</option>
            <option value="staff" <?= $userTypeFilter == 'staff' ? 'selected' : '' ?>>Staff</option>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th><a href="?order_by=user_id&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">User ID</a></th>
                <th><a href="?order_by=FullName&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">Full Name</a></th>
                <th><a href="?order_by=Username&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">Username</a></th>
                <th><a href="?order_by=Email&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">Email</a></th>
                <th><a href="?order_by=Phone&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">Phone</a></th>
                <th><a href="?order_by=UserType&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">User Type</a></th>
                <th><a href="?order_by=IsActive&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">Is Active</a></th>
                <th><a href="?order_by=LastLogin&order_dir=<?= $orderDir == 'ASC' ? 'DESC' : 'ASC' ?>">Last Login</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user_data): ?>
                    <tr>
                        <td><?= htmlspecialchars($user_data['user_id']) ?></td>
                        <td><?= htmlspecialchars($user_data['FullName']) ?></td>
                        <td><?= htmlspecialchars($user_data['Username']) ?></td>
                        <td><?= htmlspecialchars($user_data['email']) ?></td>
                        <td><?= htmlspecialchars($user_data['phone']) ?></td>
                        <td><?= htmlspecialchars($user_data['UserType']) ?></td>
                        <td><?= $user_data['IsActive'] ? 'Yes' : 'No' ?></td>
                        <td><?= htmlspecialchars($user_data['LastLogin']) ?></td>
                        <td>
                            <a href="edit_user.php?user_id=<?= $user_data['user_id'] ?>">Edit</a> | 
                            <a href="delete_user.php?user_id=<?= $user_data['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
