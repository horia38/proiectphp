<?php
// index.php
require_once 'includes/session.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isUserLoggedIn()) {
    redirect('login.php');
}

// Fetch all users
$sql = "SELECT id, username, role FROM users";
$stmt = $pdo->query($sql);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>

    <p><a href="logout.php">Logout</a></p>
    <h2>All Users</h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <?php if (isAdmin()): ?>
                    <th>Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo $user['role']; ?></td>
                <?php if (isAdmin()): ?>
                    <td>
                        <!-- A link to an edit form for this user -->
                        <a href="admin_update.php?user_id=<?php echo $user['id']; ?>">Edit</a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
